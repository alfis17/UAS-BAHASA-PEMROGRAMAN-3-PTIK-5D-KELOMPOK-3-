<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\SiswaModel;
use App\Models\KelasModel;
use CodeIgniter\Controller;

class Auth extends Controller
{
    protected $userModel;
    protected $siswaModel;
    protected $kelasModel;
    protected $session;
    
    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->siswaModel = new SiswaModel();
        $this->kelasModel = new KelasModel();
        $this->session = session();
        helper(['form', 'url', 'cookie']);
    }

    public function login()
    {
        // Cek jika sudah login
        if ($this->session->get('logged_in')) {
            $userType = $this->session->get('user_type');
            if ($userType === 'siswa') {
                return redirect()->to('siswa/dashboard_siswa');
            } else {
                return redirect()->to('/dashboard');
            }
        }
        
        // Cek remember me cookie
        if (get_cookie('remember_token')) {
            return $this->checkRememberToken();
        }
        
        $data = [
            'title' => 'Login - Sistem SPP',
            'validation' => \Config\Services::validation()
        ];
        
        return view('login_auth', $data);
    }

    public function processLogin()
    {
        $validation = \Config\Services::validation();
        
        // Set validation rules
        $rules = [
            'username' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Username/Email/NISN harus diisi'
                ]
            ],
            'password' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Password harus diisi'
                ]
            ]
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->to('/login')->withInput()->with('errors', $validation->getErrors());
        }
        
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        $remember = $this->request->getPost('remember');
        
        // Tentukan tipe user (admin/petugas vs siswa)
        $isNISN = preg_match('/^\d{10}$/', $username);
        
        if ($isNISN) {
            // LOGIN SEBAGAI SISWA
            return $this->loginSiswa($username, $password, $remember);
        } else {
            // LOGIN SEBAGAI ADMIN/PETUGAS
            return $this->loginAdminPetugas($username, $password, $remember);
        }
    }

    private function loginAdminPetugas($username, $password, $remember = false)
    {
        // Cek jika akun terkunci karena terlalu banyak percobaan gagal
        $key = 'failed_attempts_' . md5($username);
        $lockedUntil = $this->session->get($key . '_locked');
        
        if ($lockedUntil && time() < $lockedUntil) {
            $remainingTime = ceil(($lockedUntil - time()) / 60);
            $this->session->setFlashdata('error', "Akun terkunci. Coba lagi dalam {$remainingTime} menit.");
            return redirect()->to('/login');
        }
        
        // Cari user by username atau email
        $user = $this->userModel->findByUsernameOrEmail($username);
        
        if (!$user) {
            $this->logFailedAttempt($username);
            $this->session->setFlashdata('error', 'Username atau password salah');
            return redirect()->to('/login')->withInput();
        }
        
        // Cek status akun
        if (isset($user['status']) && $user['status'] != 'active') {
            $this->session->setFlashdata('error', 'Akun Anda tidak aktif. Hubungi administrator.');
            return redirect()->to('/login');
        }
        
        // Verifikasi password
        if (!password_verify($password, $user['password'])) {
            $this->logFailedAttempt($username);
            $this->session->setFlashdata('error', 'Username atau password salah');
            return redirect()->to('/login')->withInput();
        }
        
        // Update last login
        $this->userModel->updateLastLogin($user['id']);
        
        // Set session data
        $sessionData = [
            'user_id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'nama_lengkap' => $user['nama_lengkap'],
            'role' => $user['role'],
            'user_type' => 'admin_petugas',
            'logged_in' => true,
            'login_time' => time(),
            'last_activity' => time(),
            'session_expiry' => time() + (60 * 60 * 2) // 2 jam expiry
        ];
        
        $this->session->set($sessionData);
        
        // Handle remember me
        if ($remember) {
            $this->setRememberToken($user['id']);
        }
        
        // Reset failed attempts
        $this->resetFailedAttempts($username);
        
        // Redirect based on role
        return $this->redirectAfterLogin($user['role'], 'admin_petugas');
    }

    private function loginSiswa($nisn, $password, $remember = false)
{
    // Cek jika akun terkunci karena terlalu banyak percobaan gagal
    $key = 'failed_attempts_' . md5($nisn);
    $lockedUntil = $this->session->get($key . '_locked');
    
    if ($lockedUntil && time() < $lockedUntil) {
        $remainingTime = ceil(($lockedUntil - time()) / 60);
        $this->session->setFlashdata('error', "Akun terkunci. Coba lagi dalam {$remainingTime} menit.");
        return redirect()->to('/login');
    }
    
    // Cari siswa by NISN
    $siswa = $this->siswaModel->where('nisn', $nisn)->first();
    
    if (!$siswa) {
        $this->logFailedAttempt($nisn);
        $this->session->setFlashdata('error', 'NISN atau password salah');
        return redirect()->to('/login')->withInput();
    }
    
    // Cek apakah siswa sudah punya password
    $passwordVerified = false;
    
    if (empty($siswa['password'])) {
        // Jika belum punya password, password default = NISN
        if ($password === $nisn) {
            $passwordVerified = true;
            // Hash dan simpan password
            $hashedPassword = password_hash($nisn, PASSWORD_DEFAULT);
            $this->siswaModel->update($siswa['id'], ['password' => $hashedPassword]);
            $this->session->setFlashdata('info', 'Silakan ganti password default Anda di menu profile.');
        }
    } else {
        // Verifikasi password yang sudah ada (sudah di-hash)
        if (password_verify($password, $siswa['password'])) {
            $passwordVerified = true;
        } else {
            // Coba verifikasi dengan NISN sebagai fallback (untuk kasus migrasi)
            if ($password === $nisn) {
                $passwordVerified = true;
                // Update dengan hash yang baru
                $hashedPassword = password_hash($nisn, PASSWORD_DEFAULT);
                $this->siswaModel->update($siswa['id'], ['password' => $hashedPassword]);
                $this->session->setFlashdata('info', 'Password telah diupdate ke sistem baru. Silakan ganti password di menu profile.');
            }
        }
    }
    
    if (!$passwordVerified) {
        $this->logFailedAttempt($nisn);
        $this->session->setFlashdata('error', 'NISN atau password salah');
        return redirect()->to('/login')->withInput();
    }
    
    // Update last login
    $this->siswaModel->update($siswa['id'], ['last_login' => date('Y-m-d H:i:s')]);
    
    // Dapatkan data kelas
    $kelas = $this->kelasModel->find($siswa['id_kelas']);
    $nama_kelas = $kelas ? $kelas['nama_kelas'] : 'Belum ada kelas';
    
    // Set session data untuk siswa
    $sessionData = [
        'user_id' => $siswa['id'],
        'username' => $siswa['nisn'],
        'nama_lengkap' => $siswa['nama_siswa'],
        'role' => 'siswa',
        'user_type' => 'siswa',
        'nisn' => $siswa['nisn'],
        'id_kelas' => $siswa['id_kelas'],
        'nama_kelas' => $nama_kelas,
        'logged_in' => true,
        'login_time' => time(),
        'last_activity' => time(),
        'session_expiry' => time() + (60 * 60 * 2) // 2 jam expiry
    ];
    
    $this->session->set($sessionData);
    
    // Reset failed attempts
    $this->resetFailedAttempts($nisn);
    
    return $this->redirectAfterLogin('siswa', 'siswa');
}

    private function checkRememberToken()
    {
        $token = get_cookie('remember_token');
        if (!$token) return false;
        
        // Split token (type|user_id|token)
        $parts = explode('|', $token);
        if (count($parts) !== 3) {
            delete_cookie('remember_token');
            return false;
        }
        
        $type = $parts[0];
        $user_id = $parts[1];
        $remember_token = $parts[2];
        $hashed_token = hash('sha256', $remember_token);
        
        if ($type === 'admin_petugas') {
            $user = $this->userModel->find($user_id);
            if ($user && isset($user['remember_token']) && hash_equals($user['remember_token'], $hashed_token)) {
                return $this->autoLoginAdminPetugas($user);
            }
        }
        
        delete_cookie('remember_token');
        return false;
    }
    
    private function autoLoginAdminPetugas($user)
    {
        // Update token untuk security
        $new_token = bin2hex(random_bytes(32));
        $hashed_new_token = hash('sha256', $new_token);
        
        $this->userModel->update($user['id'], [
            'remember_token' => $hashed_new_token,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        
        // Set new cookie
        $cookie_value = 'admin_petugas|' . $user['id'] . '|' . $new_token;
        set_cookie('remember_token', $cookie_value, 60*60*24*30);
        
        // Set session
        $sessionData = [
            'user_id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'nama_lengkap' => $user['nama_lengkap'],
            'role' => $user['role'],
            'user_type' => 'admin_petugas',
            'logged_in' => true,
            'login_time' => time(),
            'last_activity' => time(),
            'session_expiry' => time() + (60 * 60 * 2)
        ];
       
        $this->session->set($sessionData);
        
        return redirect()->to('/dashboard');
    }
    
    private function setRememberToken($user_id)
    {
        $token = bin2hex(random_bytes(32));
        $hashed_token = hash('sha256', $token);
        
        $this->userModel->update($user_id, [
            'remember_token' => $hashed_token,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        
        $cookie_value = 'admin_petugas|' . $user_id . '|' . $token;
        set_cookie('remember_token', $cookie_value, 60*60*24*30);
    }

    private function logFailedAttempt($username)
    {
        $key = 'failed_attempts_' . md5($username);
        $attempts = $this->session->get($key, 0);
        $attempts++;
        $this->session->set($key, $attempts);
        
        // Jika 5 kali gagal, lock untuk 15 menit
        if ($attempts >= 5) {
            $lockTime = time() + 900; // 15 menit
            $this->session->set($key . '_locked', $lockTime);
        }
    }

    private function resetFailedAttempts($username)
    {
        $key = 'failed_attempts_' . md5($username);
        $this->session->remove($key);
        $this->session->remove($key . '_locked');
    }

    private function redirectAfterLogin($role, $user_type = 'admin_petugas')
    {
        $redirect_url = '/dashboard';
        
        // Redirect berdasarkan role dan user_type
        if ($user_type === 'siswa') {
            $redirect_url = 'siswa/dashboard_siswa';
        } else {
            // Untuk admin/petugas
            switch($role) {
                case 'admin':
                    $redirect_url = '/dashboard';
                    break;
                case 'petugas':
                    $redirect_url = '/dashboard';
                    break;
                default:
                    $redirect_url = '/dashboard';
                    break;
            }
        }
        
        return redirect()->to($redirect_url)->with('success', 'Login berhasil! Selamat datang.');
    }

    public function register()
    {
        if ($this->session->get('logged_in')) {
            return redirect()->to('/dashboard')->with('info', 'Anda sudah login');
        }
        
        // Ambil data kelas untuk dropdown siswa
        $kelas = $this->kelasModel->findAll();
        
        $data = [
            'title' => 'Register - Sistem SPP',
            'validation' => \Config\Services::validation(),
            'kelas_list' => $kelas,
            'tahun_sekarang' => date('Y')
        ];
        
        return view('register_auth', $data);
    }

    public function processRegister()
    {
        $validation = \Config\Services::validation();
        $role = $this->request->getPost('role');
        
        // Rules dasar untuk semua role
        $rules = [
            'role' => 'required|in_list[admin,petugas,siswa]',
            'nama_lengkap' => 'required|max_length[150]',
        ];
        
        // Tambah rules berdasarkan role
        if ($role === 'siswa') {
            $rules = array_merge($rules, [
                'nisn' => 'required|exact_length[10]|numeric|is_unique[siswa.nisn]',
                'jenis_kelamin' => 'required|in_list[L,P]',
                'tempat_lahir' => 'required|max_length[100]',
                'tanggal_lahir' => 'required|valid_date',
                'alamat' => 'required',
                'no_hp' => 'required|max_length[20]',
                'id_kelas' => 'required|numeric',
                'tahun_masuk' => 'required|numeric|exact_length[4]',
            ]);
        } else {
            $rules = array_merge($rules, [
                'username' => 'required|min_length[3]|max_length[100]|is_unique[users.username]',
                'email' => 'required|valid_email|max_length[100]|is_unique[users.email]',
                'password' => 'required|min_length[6]',
                'confirm_password' => 'required|matches[password]',
            ]);
        }
        
        if (!$this->validate($rules)) {
            return redirect()->to('/register')->withInput()->with('errors', $validation->getErrors());
        }
        
        if ($role === 'siswa') {
            // Simpan ke tabel siswa
            $nisn = $this->request->getPost('nisn');
            
            $siswaData = [
                'nisn' => $nisn,
                'nama_siswa' => $this->request->getPost('nama_lengkap'),
                'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),
                'tempat_lahir' => $this->request->getPost('tempat_lahir'),
                'tanggal_lahir' => $this->request->getPost('tanggal_lahir'),
                'alamat' => $this->request->getPost('alamat'),
                'no_hp' => $this->request->getPost('no_hp'),
                'id_kelas' => $this->request->getPost('id_kelas'),
                'tahun_masuk' => $this->request->getPost('tahun_masuk'),
                'password' => password_hash($nisn, PASSWORD_DEFAULT),
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            try {
                if ($this->siswaModel->insert($siswaData)) {
                    $this->session->setFlashdata('success', 'Registrasi siswa berhasil! Password default adalah NISN Anda.');
                    return redirect()->to('/login');
                } else {
                    $this->session->setFlashdata('error', 'Gagal melakukan registrasi siswa');
                    return redirect()->to('/register');
                }
            } catch (\Exception $e) {
                $this->session->setFlashdata('error', 'Error: ' . $e->getMessage());
                return redirect()->to('/register');
            }
        } else {
            // Simpan ke tabel users (admin/petugas)
            $userData = [
                'username' => $this->request->getPost('username'),
                'email' => $this->request->getPost('email'),
                'password' => $this->request->getPost('password'),
                'nama_lengkap' => $this->request->getPost('nama_lengkap'),
                'role' => $role,
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            try {
                if ($this->userModel->save($userData)) {
                    $this->session->setFlashdata('success', 'Registrasi ' . $role . ' berhasil! Silakan login.');
                    return redirect()->to('/login');
                } else {
                    $errors = $this->userModel->errors();
                    $this->session->setFlashdata('error', 'Gagal melakukan registrasi: ' . implode(', ', $errors));
                    return redirect()->to('/register');
                }
            } catch (\Exception $e) {
                $this->session->setFlashdata('error', 'Error: ' . $e->getMessage());
                return redirect()->to('/register');
            }
        }
    }

    public function logout()
    {
        // Hapus remember token jika ada
        if (get_cookie('remember_token')) {
            $token = get_cookie('remember_token');
            $parts = explode('|', $token);
            
            if (count($parts) === 3) {
                $type = $parts[0];
                $user_id = $parts[1];
                
                if ($type === 'admin_petugas') {
                    $this->userModel->update($user_id, ['remember_token' => null]);
                }
            }
            
            delete_cookie('remember_token');
        }
        
        // Destroy session
        $this->session->destroy();
        
        // Redirect to login with message
        return redirect()->to('/login')->with('success', 'Anda telah logout dari sistem.');
    }

    public function forgotPassword()
    {
        if ($this->session->get('logged_in')) {
            return redirect()->to('/dashboard');
        }
        
        $data = ['title' => 'Lupa Password - Sistem SPP'];
        return view('auth/forgot_password', $data);
    }

    public function processForgotPassword()
    {
        $email = $this->request->getPost('email');
        
        $user = $this->userModel->where('email', $email)->first();
        
        if ($user) {
            // Generate reset token dan kirim email
            $this->session->setFlashdata('success', 'Instruksi reset password telah dikirim ke email Anda.');
        } else {
            $this->session->setFlashdata('error', 'Email tidak ditemukan.');
        }
        
        return redirect()->to('/forgot-password');
    }

    /**
     * METHOD BARU: Untuk mengecek session dan update last_activity
     * Panggil method ini di setiap controller yang membutuhkan auth
     */
    public static function checkSession()
    {
        $session = session();
        
        // Cek jika sudah login
        if (!$session->get('logged_in')) {
            return false;
        }
        
        // Cek session expiry
        $expiry = $session->get('session_expiry');
        $lastActivity = $session->get('last_activity');
        
        // Jika session expired (2 jam) atau inactive lebih dari 30 menit
        if (time() > $expiry || (time() - $lastActivity) > (60 * 30)) {
            $session->destroy();
            return false;
        }
        
        // Update last activity
        $session->set('last_activity', time());
        
        return true;
    }
}