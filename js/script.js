// SCRIPT.JS - JavaScript Utama Website Lostly
// Menggunakan ES6 dan HTML DOM

// 1. LOADING OVERLAY
// Menampilkan loading saat halaman dimuat
document.addEventListener('DOMContentLoaded', () => {
    const loadingOverlay = document.getElementById('loadingOverlay');
    if (loadingOverlay) {
        // Fade out loading setelah halaman selesai dimuat
        setTimeout(() => {
            loadingOverlay.classList.add('fade-out');
            // Hapus elemen loading dari DOM setelah animasi selesai
            setTimeout(() => {
                loadingOverlay.remove();
            }, 500);
        }, 800);
    }
});

// 2. NAVBAR SCROLL EFFECT
// Mengubah tampilan navbar saat user scroll
const navbar = document.querySelector('.navbar-lostly');
if (navbar) {
    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });
}

// 3. SCROLL ANIMATION (AOS-like)
// Animasi elemen saat muncul di viewport
const observerOptions = {
    root: null,
    rootMargin: '0px',
    threshold: 0.15
};

const scrollObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('animated');
            // Berhenti observasi setelah animasi
            scrollObserver.unobserve(entry.target);
        }
    });
}, observerOptions);

// Observasi semua elemen dengan class animate-on-scroll
document.querySelectorAll('.animate-on-scroll').forEach(el => {
    scrollObserver.observe(el);
});

// 4. TOGGLE PASSWORD VISIBILITY
// Menampilkan/sembunyikan password di form
function initTogglePassword() {
    const toggleBtns = document.querySelectorAll('.toggle-password');

    toggleBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            // Ambil input yang terkait
            const input = btn.closest('.form-floating-custom').querySelector('input');

            if (input.type === 'password') {
                // Ubah ke text agar password terlihat
                input.type = 'text';
                btn.innerHTML = '<i class="bi bi-eye-slash"></i>';
            } else {
                // Ubah kembali ke password
                input.type = 'password';
                btn.innerHTML = '<i class="bi bi-eye"></i>';
            }
        });
    });
}

// Panggil saat DOM ready
document.addEventListener('DOMContentLoaded', initTogglePassword);

// 5. IMAGE UPLOAD PREVIEW
// Menampilkan preview gambar sebelum upload
function initImagePreview() {
    const fileInputs = document.querySelectorAll('.file-upload-input');

    fileInputs.forEach(input => {
        input.addEventListener('change', function() {
            const previewContainer = document.getElementById(this.dataset.preview);

            if (this.files && this.files[0]) {
                const reader = new FileReader();

                reader.onload = (e) => {
                    // Tampilkan preview gambar
                    if (previewContainer) {
                        previewContainer.innerHTML = `
                            <img src="${e.target.result}" class="preview-image" alt="Preview">
                            <p class="mt-2 mb-0" style="color: rgba(255,255,255,0.6); font-size: 0.85rem;">
                                <i class="bi bi-check-circle text-success"></i> ${this.files[0].name}
                            </p>
                        `;
                    }
                };

                reader.readAsDataURL(this.files[0]);
            }
        });
    });

    // Click upload area untuk trigger file input
    const uploadAreas = document.querySelectorAll('.upload-area');
    uploadAreas.forEach(area => {
        area.addEventListener('click', () => {
            const input = area.querySelector('.file-upload-input');
            if (input) input.click();
        });

        // Drag and drop support
        area.addEventListener('dragover', (e) => {
            e.preventDefault();
            area.style.borderColor = '#3B82F6';
            area.style.background = 'rgba(59, 130, 246, 0.05)';
        });

        area.addEventListener('dragleave', () => {
            area.style.borderColor = '';
            area.style.background = '';
        });

        area.addEventListener('drop', (e) => {
            e.preventDefault();
            area.style.borderColor = '';
            area.style.background = '';
            const input = area.querySelector('.file-upload-input');
            if (input && e.dataTransfer.files.length > 0) {
                input.files = e.dataTransfer.files;
                // Trigger change event
                input.dispatchEvent(new Event('change'));
            }
        });
    });
}

document.addEventListener('DOMContentLoaded', initImagePreview);

// 6. FORM VALIDATION
// Validasi form di sisi client menggunakan HTML DOM
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return true;

    let isValid = true;
    const requiredFields = form.querySelectorAll('[required]');

    requiredFields.forEach(field => {
        // Hapus pesan error sebelumnya
        removeError(field);

        if (!field.value.trim()) {
            // Tampilkan error jika field kosong
            showError(field, 'Field ini wajib diisi');
            isValid = false;
        }

        // Validasi email
        if (field.type === 'email' && field.value.trim()) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(field.value)) {
                showError(field, 'Format email tidak valid');
                isValid = false;
            }
        }

        // Validasi password minimum 6 karakter
        if (field.type === 'password' && field.value.trim()) {
            if (field.value.length < 6) {
                showError(field, 'Password minimal 6 karakter');
                isValid = false;
            }
        }
    });

    // Validasi konfirmasi password
    const password = form.querySelector('#password');
    const confirmPassword = form.querySelector('#confirm_password');
    if (password && confirmPassword && confirmPassword.value) {
        if (password.value !== confirmPassword.value) {
            showError(confirmPassword, 'Password tidak cocok');
            isValid = false;
        }
    }

    return isValid;
}

// Tampilkan pesan error di bawah field
function showError(field, message) {
    field.style.borderColor = '#EF4444';
    const errorDiv = document.createElement('div');
    errorDiv.className = 'field-error';
    errorDiv.style.cssText = 'color: #FCA5A5; font-size: 0.8rem; margin-top: 6px; display: flex; align-items: center; gap: 4px;';
    errorDiv.innerHTML = `<i class="bi bi-exclamation-circle"></i> ${message}`;
    field.parentNode.appendChild(errorDiv);
}

// Hapus pesan error
function removeError(field) {
    field.style.borderColor = '';
    const existingError = field.parentNode.querySelector('.field-error');
    if (existingError) existingError.remove();
}

// 7. REAL-TIME SEARCH
// Filter data barang secara real-time
function initRealTimeSearch() {
    const searchInput = document.getElementById('searchBarang');

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            const cards = document.querySelectorAll('.barang-card-wrapper');

            cards.forEach(card => {
                // Ambil teks dari card untuk dicocokkan
                const text = card.textContent.toLowerCase();

                if (text.includes(query)) {
                    card.style.display = '';
                    card.style.animation = 'fadeIn 0.3s ease';
                } else {
                    card.style.display = 'none';
                }
            });

            // Tampilkan empty state jika tidak ada hasil
            const visibleCards = document.querySelectorAll('.barang-card-wrapper:not([style*="display: none"])');
            const emptyState = document.getElementById('emptySearchState');
            if (emptyState) {
                emptyState.style.display = visibleCards.length === 0 ? 'block' : 'none';
            }
        });
    }
}

document.addEventListener('DOMContentLoaded', initRealTimeSearch);

// 8. FILTER BARANG
// Filter berdasarkan kategori, status, lokasi
function filterBarang() {
    const kategori = document.getElementById('filterKategori')?.value || '';
    const status = document.getElementById('filterStatus')?.value || '';
    const tanggal = document.getElementById('filterTanggal')?.value || '';
    const cards = document.querySelectorAll('.barang-card-wrapper');

    cards.forEach(card => {
        const cardKategori = card.dataset.kategori || '';
        const cardStatus = card.dataset.status || '';
        const cardTanggal = card.dataset.tanggal || '';

        let show = true;

        // Cek filter kategori
        if (kategori && cardKategori !== kategori) show = false;
        // Cek filter status
        if (status && cardStatus !== status) show = false;
        // Cek filter tanggal
        if (tanggal && cardTanggal !== tanggal) show = false;

        card.style.display = show ? '' : 'none';
    });

    // Cek empty state
    const visibleCards = document.querySelectorAll('.barang-card-wrapper:not([style*="display: none"])');
    const emptyState = document.getElementById('emptySearchState');
    if (emptyState) {
        emptyState.style.display = visibleCards.length === 0 ? 'block' : 'none';
    }
}

// 9. SWEETALERT2 - KONFIRMASI HAPUS
// Dialog konfirmasi sebelum menghapus data
function confirmDelete(id, type) {
    Swal.fire({
        title: 'Hapus Data?',
        text: 'Data yang dihapus tidak dapat dikembalikan!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#EF4444',
        cancelButtonColor: '#64748B',
        confirmButtonText: '<i class="bi bi-trash"></i> Ya, Hapus!',
        cancelButtonText: 'Batal',
        customClass: {
            popup: 'swal-custom-popup'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Redirect ke halaman hapus
            window.location.href = `hapus_barang.php?id=${id}&type=${type}`;
        }
    });
}

// 10. SWEETALERT2 - KONFIRMASI LOGOUT
// Dialog konfirmasi sebelum logout
function confirmLogout() {
    Swal.fire({
        title: 'Logout?',
        text: 'Apakah Anda yakin ingin keluar?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#0033FF',
        cancelButtonColor: '#64748B',
        confirmButtonText: '<i class="bi bi-box-arrow-right"></i> Ya, Logout',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'logout.php';
        }
    });
}

// 11. LOADING BUTTON
// Menampilkan spinner saat form disubmit
function initLoadingButton() {
    const forms = document.querySelectorAll('form[data-loading]');

    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');

            if (submitBtn && !submitBtn.disabled) {
                // Simpan teks asli
                const originalText = submitBtn.innerHTML;
                // Tampilkan spinner
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Memproses...';
                submitBtn.disabled = true;

                // Re-enable setelah 10 detik (fallback)
                setTimeout(() => {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }, 10000);
            }
        });
    });
}

document.addEventListener('DOMContentLoaded', initLoadingButton);

// 12. COUNTER ANIMATION
// Animasi angka naik untuk statistik
function animateCounters() {
    const counters = document.querySelectorAll('.counter-value');

    counters.forEach(counter => {
        const target = parseInt(counter.dataset.target);
        const duration = 2000; // 2 detik
        const step = target / (duration / 16);
        let current = 0;

        const updateCounter = () => {
            current += step;
            if (current < target) {
                counter.textContent = Math.ceil(current);
                requestAnimationFrame(updateCounter);
            } else {
                counter.textContent = target;
            }
        };

        updateCounter();
    });
}

// Trigger counter animation saat section terlihat
const statsSection = document.querySelector('.stats-section');
if (statsSection) {
    const statsObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateCounters();
                statsObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.3 });

    statsObserver.observe(statsSection);
}

// 13. SMOOTH SCROLL - Navigasi Halus
// Scroll halus ke section tertentu
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
            // Tutup navbar mobile jika terbuka
            const navCollapse = document.querySelector('.navbar-collapse');
            if (navCollapse && navCollapse.classList.contains('show')) {
                const bsCollapse = new bootstrap.Collapse(navCollapse, { toggle: true });
            }
        }
    });
});

// 14. NAVBAR ACTIVE LINK - Landing Page
// Highlight link aktif berdasarkan scroll position
function updateActiveNav() {
    const sections = document.querySelectorAll('section[id]');
    const navLinks = document.querySelectorAll('.navbar-lostly .nav-link[href^="#"]');

    sections.forEach(section => {
        const sectionTop = section.offsetTop - 100;
        const sectionHeight = section.clientHeight;
        const scrollY = window.scrollY;

        if (scrollY >= sectionTop && scrollY < sectionTop + sectionHeight) {
            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === '#' + section.id) {
                    link.classList.add('active');
                }
            });
        }
    });
}

window.addEventListener('scroll', updateActiveNav);

// 15. SWEETALERT DARI URL PARAMETER
// Menampilkan SweetAlert berdasarkan parameter URL
document.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    const alert = urlParams.get('alert');

    // Object berisi konfigurasi SweetAlert untuk setiap jenis alert
    const alertConfigs = {
        'login_success': {
            icon: 'success',
            title: 'Login Berhasil!',
            text: 'Selamat datang kembali di Lostly',
            timer: 2000,
            showConfirmButton: false
        },
        'login_failed': {
            icon: 'error',
            title: 'Login Gagal!',
            text: 'Email atau password salah'
        },
        'register_success': {
            icon: 'success',
            title: 'Registrasi Berhasil!',
            text: 'Silakan login dengan akun Anda',
            timer: 2500,
            showConfirmButton: false
        },
        'register_failed': {
            icon: 'error',
            title: 'Registrasi Gagal!',
            text: urlParams.get('msg') || 'Terjadi kesalahan saat registrasi'
        },
        'tambah_success': {
            icon: 'success',
            title: 'Data Berhasil Ditambahkan!',
            text: 'Laporan Anda telah tersimpan',
            timer: 2000,
            showConfirmButton: false
        },
        'edit_success': {
            icon: 'success',
            title: 'Data Berhasil Diupdate!',
            text: 'Perubahan telah disimpan',
            timer: 2000,
            showConfirmButton: false
        },
        'delete_success': {
            icon: 'success',
            title: 'Data Berhasil Dihapus!',
            text: 'Data telah dihapus dari sistem',
            timer: 2000,
            showConfirmButton: false
        },
        'logout_success': {
            icon: 'success',
            title: 'Logout Berhasil!',
            text: 'Sampai jumpa kembali',
            timer: 2000,
            showConfirmButton: false
        },
        'needlogin': {
            icon: 'warning',
            title: 'Akses Ditolak!',
            text: 'Silakan login terlebih dahulu'
        },
        'profil_success': {
            icon: 'success',
            title: 'Profil Diperbarui!',
            text: 'Data profil Anda telah diupdate',
            timer: 2000,
            showConfirmButton: false
        },
        'password_success': {
            icon: 'success',
            title: 'Password Diubah!',
            text: 'Password Anda berhasil diperbarui',
            timer: 2000,
            showConfirmButton: false
        },
        'password_failed': {
            icon: 'error',
            title: 'Gagal!',
            text: urlParams.get('msg') || 'Password lama tidak sesuai'
        },
        'email_exists': {
            icon: 'error',
            title: 'Registrasi Gagal!',
            text: 'Email sudah terdaftar, gunakan email lain'
        },
        'success': {
            icon: 'success',
            title: 'Berhasil!',
            text: 'Aksi berhasil diproses.',
            timer: 2000,
            showConfirmButton: false
        },
        'deleted': {
            icon: 'success',
            title: 'Dihapus!',
            text: 'Data berhasil dihapus dari sistem.',
            timer: 2000,
            showConfirmButton: false
        },
        'disabled': {
            icon: 'error',
            title: 'Akun Dinonaktifkan!',
            text: 'Akun Anda dinonaktifkan oleh administrator. Silakan hubungi admin.'
        }
    };

    // Tampilkan SweetAlert jika ada parameter alert
    if (alert && alertConfigs[alert]) {
        Swal.fire(alertConfigs[alert]);
    }
});

// 16. RESET FILTER
// Reset semua filter ke default
function resetFilter() {
    const filterKategori = document.getElementById('filterKategori');
    const filterStatus = document.getElementById('filterStatus');
    const filterTanggal = document.getElementById('filterTanggal');
    const searchInput = document.getElementById('searchBarang');

    if (filterKategori) filterKategori.value = '';
    if (filterStatus) filterStatus.value = '';
    if (filterTanggal) filterTanggal.value = '';
    if (searchInput) searchInput.value = '';

    // Tampilkan semua card
    const cards = document.querySelectorAll('.barang-card-wrapper');
    cards.forEach(card => {
        card.style.display = '';
    });

    // Sembunyikan empty state
    const emptyState = document.getElementById('emptySearchState');
    if (emptyState) emptyState.style.display = 'none';
}

// 17. SIDEBAR TOGGLE
// Logic untuk membuka dan menutup sidebar di mobile
function initSidebar() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('show');
        });
        
        // Tutup sidebar saat klik di luar
        document.addEventListener('click', (e) => {
            if (window.innerWidth <= 991 && sidebar.classList.contains('show')) {
                if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                    sidebar.classList.remove('show');
                }
            }
        });
    }
}

document.addEventListener('DOMContentLoaded', initSidebar);
