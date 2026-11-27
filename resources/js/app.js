import QRCode from 'qrcode';

window.renderTiketQr = async function (canvasId, kodeTiket) {
    const canvas = document.getElementById(canvasId);
    if (!canvas || !kodeTiket) return;

    try {
        await QRCode.toCanvas(canvas, kodeTiket, {
            width: 200,
            margin: 2,
        });
    } catch (error) {
        console.error('Gagal merender QR Code tiket:', error);
    }
};

// Filter direktori UKM di landing. Tanpa JavaScript, semua kartu tetap tampil.
const initUkmFilter = () => {
    const root = document.querySelector('[data-ukm-filter]');

    if (!root) return;

    const buttons = Array.from(root.querySelectorAll('[data-ukm-filter-button]'));
    const items = Array.from(root.querySelectorAll('[data-ukm-status]'));
    // Penghitung dan pesan kosong berada di luar wadah filter, jadi dicari dari dokumen.
    const counter = document.querySelector('[data-ukm-count]');
    const empty = document.querySelector('[data-ukm-empty]');

    const apply = (filter, activeButton) => {
        let visible = 0;

        buttons.forEach((button) => {
            button.setAttribute('aria-pressed', String(button === activeButton));
        });

        items.forEach((item) => {
            const cocok = filter === 'semua' || item.dataset.ukmStatus === filter;

            item.hidden = ! cocok;

            if (cocok) visible += 1;
        });

        if (counter) counter.textContent = String(visible);
        if (empty) empty.hidden = visible > 0;
    };

    buttons.forEach((button) => {
        button.addEventListener('click', () => apply(button.dataset.ukmFilterButton, button));
    });
};

const themeIsDark = () => document.documentElement.classList.contains('dark');

const syncThemeToggles = (isDark = themeIsDark()) => {
    const label = isDark ? 'Ganti ke mode terang' : 'Ganti ke mode gelap';

    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
        button.setAttribute('aria-pressed', String(isDark));
        button.setAttribute('aria-label', label);
        button.setAttribute('title', label);
    });
};

const applyTheme = (appearance) => {
    // Flux mengekspos store reaktifnya sebagai window.Flux pada halaman ber-Alpine,
    // sedangkan halaman tanpa Alpine (landing) hanya punya applyAppearance().
    if (window.Flux && 'appearance' in window.Flux) {
        window.Flux.appearance = appearance;

        return;
    }

    window.Flux?.applyAppearance?.(appearance);
};

document.addEventListener('click', (event) => {
    const button = event.target.closest('[data-theme-toggle]');

    if (!button) return;

    const nextIsDark = ! themeIsDark();

    applyTheme(nextIsDark ? 'dark' : 'light');
    syncThemeToggles(nextIsDark);
});

const sidebarIsCollapsed = () => document.documentElement.getAttribute('data-sidebar') === 'collapsed';

const restoreSidebarState = () => {
    try {
        if (window.localStorage.getItem('sip.sidebar') === 'collapsed') {
            document.documentElement.setAttribute('data-sidebar', 'collapsed');
        }
    } catch (error) {
        // localStorage bisa diblokir; biarkan sidebar dalam keadaan terbuka.
    }
};

const syncSidebarToggles = () => {
    const collapsed = sidebarIsCollapsed();
    const label = collapsed ? 'Lebarkan sidebar' : 'Ciutkan sidebar';

    document.querySelectorAll('[data-sidebar-collapse]').forEach((button) => {
        button.setAttribute('aria-expanded', String(! collapsed));
        button.setAttribute('aria-label', label);
        button.setAttribute('title', label);
    });
};

document.addEventListener('click', (event) => {
    if (! event.target.closest('[data-sidebar-collapse]')) return;

    const collapsed = sidebarIsCollapsed();

    if (collapsed) {
        document.documentElement.removeAttribute('data-sidebar');
    } else {
        document.documentElement.setAttribute('data-sidebar', 'collapsed');
    }

    try {
        if (collapsed) {
            window.localStorage.removeItem('sip.sidebar');
        } else {
            window.localStorage.setItem('sip.sidebar', 'collapsed');
        }
    } catch (error) {
        // Preferensi tidak tersimpan, tetapi sidebar tetap bisa diciutkan.
    }

    syncSidebarToggles();
});

document.addEventListener('DOMContentLoaded', () => {
    syncThemeToggles();
    syncSidebarToggles();
    initUkmFilter();
});

document.addEventListener('livewire:navigated', () => {
    // Livewire mengganti atribut <html> saat navigasi, jadi keadaan ciut dipasang ulang.
    restoreSidebarState();
    syncThemeToggles();
    syncSidebarToggles();
});
