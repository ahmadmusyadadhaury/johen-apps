// Alpine.js sudah termasuk dalam bundle Livewire 4 (@livewireScripts)
// Tidak perlu import terpisah untuk menghindari konflik dua instance Alpine.
//
// Livewire 4 juga menyediakan magic property $wire untuk entangle.
// Jika ada kode Alpine custom, gunakan window.Alpine yang disediakan Livewire.

import JsBarcode from 'jsbarcode';

// Ekspos ke window agar bisa dipakai di directive x-init "@barcode" pada halaman Asset.
window.JsBarcode = JsBarcode;

document.addEventListener('alpine:init', () => {
    // Toast notifications (top-right)
    Alpine.store('toast', {
        items: [],

        add(type, message, duration = 4000) {
            const id = Date.now() + Math.random();
            this.items.push({ id, type, message, duration });

            if (duration > 0) {
                setTimeout(() => { this.remove(id); }, duration);
            }
        },

        remove(id) {
            this.items = this.items.filter(i => i.id !== id);
        },

        success(message, duration) { this.add('success', message, duration); },
        error(message, duration) { this.add('error', message, duration); },
        warning(message, duration) { this.add('warning', message, duration); },
        info(message, duration) { this.add('info', message, duration); },
    });

    // Confirm modal (centered, untuk konfirmasi hapus)
    Alpine.store('confirmModal', {
        open: false,
        title: 'Konfirmasi',
        message: 'Apakah Anda yakin?',
        onConfirm: null,
        onCancel: null,

        show(title, message, onConfirm, onCancel) {
            this.title = title;
            this.message = message;
            this.onConfirm = onConfirm || null;
            this.onCancel = onCancel || null;
            this.open = true;
        },

        confirm() {
            if (this.onConfirm) this.onConfirm();
            this.open = false;
        },

        cancel() {
            if (this.onCancel) this.onCancel();
            this.open = false;
        },

        hide() {
            this.open = false;
            this.onConfirm = null;
            this.onCancel = null;
        },
    });

    // Success modal (centered, for CRUD operations)
    Alpine.store('successModal', {
        open: false,
        message: '',

        show(msg) {
            this.message = msg;
            this.open = true;
        },

        hide() {
            this.open = false;
        },
    });
});
