<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Cipta Grafika Estimator System</title>
    <script>
        ! function() {
            try {
                var t = localStorage.getItem("dash26-theme"),
                    e = window.matchMedia("(prefers-color-scheme: dark)").matches;
                document.documentElement.setAttribute("data-theme", t || (e ? "dark" : "light"))
            } catch (t) {
                document.documentElement.setAttribute("data-theme", "light")
            }
        }()
    </script>
    <script defer="defer" src="runtime.js"></script>
    <script defer="defer" src="vendor-fullcalendar.js"></script>
    <script defer="defer" src="vendor-chartjs.js"></script>
    <script defer="defer" src="vendors.js"></script>
    <script defer="defer" src="2026.js"></script>
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
</head>

<body>
    @if (session('success'))
        <div class="toast toast-success" id="notificationToast">

            <div class="toast-icon">
                <svg viewBox="0 0 24 24">
                    <path d="M20 6 9 17l-5-5" />
                </svg>
            </div>

            <div class="toast-content">
                <strong>Berhasil</strong>
                <span>{{ session('success') }}</span>
            </div>

            <button type="button" class="toast-close" id="closeNotification">
                <svg viewBox="0 0 24 24">
                    <path d="M18 6 6 18M6 6l12 12" />
                </svg>
            </button>

        </div>
    @endif


    @if ($errors->any())
        <div class="toast toast-error" id="notificationToast">

            <div class="toast-icon">
                <svg viewBox="0 0 24 24">
                    <path d="M18 6 6 18M6 6l12 12" />
                </svg>
            </div>

            <div class="toast-content">
                <strong>Login Gagal</strong>

                <span>{{ $errors->first('email') }}</span>
            </div>

            <button type="button" class="toast-close" id="closeNotification">
                <svg viewBox="0 0 24 24">
                    <path d="M18 6 6 18M6 6l12 12" />
                </svg>
            </button>

        </div>
    @endif

    <div class="auth-shell">
        <aside class="auth-aside">
            <div class="auth-brand">
                <div class="name">Cipta Grafika</div>
            </div>
            <div class="auth-aside-body"><span class="auth-aside-eyebrow">Sistem Administrasi</span>
                <h1>Kelola katalog produk dan konfigurasi estimasi harga.</h1>
                <p>Kelola produk, material, harga, dan aturan perhitungan secara terstruktur dalam satu sistem
                    terintegrasi.</p>
                <div class="auth-quote">"Satu sistem untuk mengelola data, harga, dan konfigurasi perhitungan Cipta
                    Grafika."<div class="auth-quote-author">
                        <div class="av">CG</div>
                        <div>Cipta Grafika · Sistem Administrasi</div>
                    </div>
                </div>
            </div>
            <div class="auth-aside-footer"><span>© 2026</span> <span>CIPTA GRAFIKA</span></div>
        </aside>
        <main class="auth-main">
            <div class="auth-card">
                <h2>Selamat Datang</h2>
                <p class="sub">Masuk untuk mengakses sistem Catalog Estimator Cipta Grafika.</p>
                <form class="auth-form" action="{{ route('admin_signin_process') }}" method="POST">
                    @csrf
                    <div class="field"><label class="field-label" for="email">Email</label>
                        <div class="input-icon"><span class="ico"><svg viewBox="0 0 24 24">
                                    <rect x="3" y="5" width="18" height="14" rx="2" />
                                    <path d="m3 7 9 6 9-6" />
                                </svg></span><input id="email" class="input" type="email" name="email"
                                placeholder="you@company.com" autocomplete="email" required></div>
                    </div>
                    <div class="field">
                        <div class="field-row"><label class="field-label" for="password">Password</label> <a
                                href="#">Lupa kata sandi?</a></div>
                        <div class="input-icon"><span class="ico"><svg viewBox="0 0 24 24">
                                    <rect x="3" y="11" width="18" height="11" rx="2" />
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                </svg></span><input id="password" class="input" type="password" name="password"
                                placeholder="••••••••" autocomplete="current-password" required></div>
                    </div><label class="check"><input type="checkbox" checked="checked" name="remember" value="1">
                        <span class="box"></span>
                        Keep me signed in for 30 days</label> <button class="btn btn--primary auth-submit"
                        type="submit">Masuk <svg viewBox="0 0 24 24">
                            <path d="M5 12h14M13 5l7 7-7 7" />
                        </svg></button>
                </form>
            </div>
            <div class="auth-main-bottom">By signing in you agree to our <a href="#">Terms</a> and <a
                    href="#">Privacy Policy</a>.</div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const toast = document.getElementById('notificationToast');
            const closeButton = document.getElementById('closeNotification');

            if (!toast) {
                return;
            }

            let isClosing = false;

            function closeToast() {

                if (isClosing) {
                    return;
                }

                isClosing = true;

                toast.style.opacity = '0';
                toast.style.transform = 'translateX(30px)';

                setTimeout(function() {
                    toast.remove();
                }, 300);
            }

            // Tombol ✕
            if (closeButton) {
                closeButton.addEventListener('click', closeToast);
            }

            // Auto close setelah 5 detik
            setTimeout(closeToast, 5000);

        });
    </script>
</body>

</html>
