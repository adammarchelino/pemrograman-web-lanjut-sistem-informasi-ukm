<?php

namespace App\Livewire\Kegiatan;

use App\Models\Kegiatan;
use App\Models\RegistrasiKegiatan;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Kegiatan UKM · SIP-UKM')]
class Index extends Component
{
    public bool $tampilkanForm = false;

    public bool $tampilkanDetail = false;

    public bool $tampilkanPeserta = false;

    public ?int $pesertaKegiatanId = null;

    public ?int $editKegiatanId = null;

    public ?int $detailKegiatanId = null;

    public string $nama = '';

    public string $deskripsi = '';

    public string $mulai_pada = '';

    public string $selesai_pada = '';

    public string $lokasi = '';

    public string $kapasitas = '';

    public string $audiens = 'mahasiswa_poliwangi';

    public string $pendaftaran_dibuka_pada = '';

    public string $pendaftaran_ditutup_pada = '';

    public string $status = 'draf';

    // Pemeriksaan pengguna terpusat agar Intelephense mengenali model User.

    private function penggunaAktif(): User
    {
        $user = Auth::user();
        abort_unless($user instanceof User && $user->isActive(), 403);

        return $user;
    }

    private function bolehKelola(User $user): bool
    {
        return $user->isPengurusUkm() && $user->ukm_id !== null;
    }

    private function bolehDaftar(User $user): bool
    {
        // isMahasiswa() pada model juga dapat bernilai true untuk pengurus.
        // Pada fitur ini, pendaftaran mandiri khusus akun mahasiswa non-pengurus.
        return $user->isMahasiswa() && ! $user->isPengurusUkm();
    }

    public function bukaForm(): void
    {
        $user = $this->penggunaAktif();
        abort_unless(
            $this->bolehKelola($user),
            403
        );
        // Pastikan form kembali ke mode Tambah.
        $this->tutupForm();
        $this->editKegiatanId = null;
        $this->detailKegiatanId = null;
        $this->tampilkanForm = true;
    }

    public function tutupForm(): void
    {
        $this->reset([
            'nama',
            'deskripsi',
            'mulai_pada',
            'selesai_pada',
            'lokasi',
            'kapasitas',
            'pendaftaran_dibuka_pada',
            'pendaftaran_ditutup_pada',
        ]);
        $this->resetValidation();
        $this->audiens = 'mahasiswa_poliwangi';
        $this->status = 'draf';
        $this->editKegiatanId = null;
        $this->tampilkanForm = false;
    }

    public function editKegiatan(int $id): void
    {
        $user = $this->penggunaAktif();
        abort_unless(
            $this->bolehKelola($user),
            403
        );
        // Hanya kegiatan dari UKM pengurus yang boleh diedit.
        $kegiatan = Kegiatan::query()
            ->where('ukm_id', $user->ukm_id)
            ->findOrFail($id);
        $this->resetValidation();
        $this->editKegiatanId = $kegiatan->id;
        $this->nama = $kegiatan->nama;
        $this->deskripsi = $kegiatan->deskripsi ?? '';
        $this->mulai_pada =
            $kegiatan->mulai_pada->format('Y-m-d\TH:i');
        $this->selesai_pada =
            $kegiatan->selesai_pada->format('Y-m-d\TH:i');
        $this->lokasi = $kegiatan->lokasi;
        $this->kapasitas = (string) $kegiatan->kapasitas;
        $this->audiens = $kegiatan->audiens;
        $this->pendaftaran_dibuka_pada =
            $kegiatan->pendaftaran_dibuka_pada
                ?->format('Y-m-d\TH:i') ?? '';
        $this->pendaftaran_ditutup_pada =
            $kegiatan->pendaftaran_ditutup_pada
                ?->format('Y-m-d\TH:i') ?? '';
        $this->status = $kegiatan->status;
        // Tutup detail jika sebelumnya sedang dibuka.
        $this->detailKegiatanId = null;
        $this->tampilkanForm = true;
    }

    public function simpan(): void
    {
        $user = $this->penggunaAktif();
        abort_unless($this->bolehKelola($user), 403);
        $data = $this->validate([
            'nama' => ['required', 'string', 'max:150'],
            'deskripsi' => ['required', 'string'],
            'mulai_pada' => ['required', 'date'],
            'selesai_pada' => ['required', 'date', 'after:mulai_pada'],
            'lokasi' => ['required', 'string', 'max:255'],
            'kapasitas' => ['required', 'integer', 'min:1'],
            'audiens' => ['required', Rule::in(['mahasiswa_poliwangi', 'umum'])],
            'pendaftaran_dibuka_pada' => ['nullable', 'date', 'before_or_equal:pendaftaran_ditutup_pada'],
            'pendaftaran_ditutup_pada' => ['nullable', 'date', 'before_or_equal:mulai_pada'],
            'status' => ['required', Rule::in(['draf', 'dipublikasikan'])],
        ]);
        $data['ukm_id'] = $user->ukm_id;
        $data['pendaftaran_dibuka_pada'] = $data['pendaftaran_dibuka_pada'] ?: null;
        $data['pendaftaran_ditutup_pada'] = $data['pendaftaran_ditutup_pada'] ?: null;
        // Lock yang sama dengan daftarKegiatan() menghindari kuota
        // berkurang ketika mahasiswa sedang mendaftar.
        DB::transaction(function () use ($user, $data): void {
            if ($this->editKegiatanId === null) {
                Kegiatan::create($data);

                return;
            }
            $kegiatan = Kegiatan::query()
                ->where('ukm_id', $user->ukm_id)
                ->lockForUpdate()
                ->findOrFail($this->editKegiatanId);
            $jumlahPeserta = $kegiatan->registrasiKegiatan()
                ->where('status', 'terdaftar')
                ->count();
            if ((int) $data['kapasitas'] < $jumlahPeserta) {
                throw ValidationException::withMessages([
                    'kapasitas' => "Kapasitas tidak boleh kurang dari {$jumlahPeserta} peserta yang sudah terdaftar.",
                ]);
            }
            $kegiatan->update($data);
        }, 3);
        $pesan = $this->editKegiatanId === null
            ? 'Kegiatan berhasil ditambahkan!'
            : 'Kegiatan berhasil diperbarui!';
        $this->tutupForm();
        session()->flash('success', $pesan);
    }

    public function lihatDetail(int $id): void
    {
        $user = $this->penggunaAktif();
        $query = Kegiatan::query();
        // Pengurus hanya bisa melihat kegiatan UKM sendiri.
        if (
            $this->bolehKelola($user)
        ) {
            $query->where('ukm_id', $user->ukm_id);
        } else {
            // Mahasiswa hanya melihat kegiatan yang dipublikasikan.
            $query->where('status', 'dipublikasikan');
        }
        $kegiatan = $query->findOrFail($id);
        $this->detailKegiatanId = $kegiatan->id;
        $this->tampilkanDetail = true;
    }

    public function tutupDetail(): void
    {
        $this->detailKegiatanId = null;
        $this->tampilkanDetail = false;
    }

    public function updatedTampilkanDetail(bool $value): void
    {
        if (! $value) {
            $this->detailKegiatanId = null;
        }
    }

    public function updatedTampilkanForm(bool $value): void
    {
        if (! $value) {
            $this->tutupForm();
        }
    }

    public function daftarKegiatan(int $id): void
    {
        $user = $this->penggunaAktif();
        // Hanya pengguna aktif dengan status mahasiswa.
        abort_unless(
            $this->bolehDaftar($user),
            403
        );
        DB::transaction(function () use ($id, $user) {
            // Kunci kegiatan saat proses pendaftaran.
            $kegiatan = Kegiatan::query()
                ->lockForUpdate()
                ->findOrFail($id);
            // Kegiatan harus sudah dipublikasikan.
            if ($kegiatan->status !== 'dipublikasikan') {
                throw ValidationException::withMessages([
                    'pendaftaran' => 'Kegiatan belum dipublikasikan.',
                ]);
            }
            // Pastikan periode pendaftaran sudah ditentukan.
            if (
                $kegiatan->pendaftaran_dibuka_pada === null
                || $kegiatan->pendaftaran_ditutup_pada === null
            ) {
                throw ValidationException::withMessages([
                    'pendaftaran' => 'Jadwal pendaftaran belum tersedia.',
                ]);
            }
            $sekarang = now();
            if ($sekarang->lt($kegiatan->pendaftaran_dibuka_pada)) {
                throw ValidationException::withMessages([
                    'pendaftaran' => 'Pendaftaran belum dibuka.',
                ]);
            }
            if (
                $sekarang->gt($kegiatan->pendaftaran_ditutup_pada)
                || $sekarang->gte($kegiatan->mulai_pada)
            ) {
                throw ValidationException::withMessages([
                    'pendaftaran' => 'Pendaftaran sudah ditutup.',
                ]);
            }
            // Cegah mahasiswa mendaftar dua kali.
            $sudahTerdaftar = RegistrasiKegiatan::query()
                ->where('kegiatan_id', $kegiatan->id)
                ->where('user_id', $user->id)
                ->where('status', 'terdaftar')
                ->exists();
            if ($sudahTerdaftar) {
                throw ValidationException::withMessages([
                    'pendaftaran' => 'Kamu sudah terdaftar pada kegiatan ini.',
                ]);
            }
            // Hitung jumlah peserta aktif.
            $jumlahPeserta = RegistrasiKegiatan::query()
                ->where('kegiatan_id', $kegiatan->id)
                ->where('status', 'terdaftar')
                ->count();
            if ($jumlahPeserta >= $kegiatan->kapasitas) {
                throw ValidationException::withMessages([
                    'pendaftaran' => 'Kuota kegiatan sudah penuh.',
                ]);
            }
            // Simpan registrasi mahasiswa.
            RegistrasiKegiatan::create([
                'kegiatan_id' => $kegiatan->id,
                'user_id' => $user->id,
                'jenis_peserta' => 'mahasiswa_poliwangi',
                'nama_peserta' => $user->name,
                'email_peserta' => $user->email,
                'nomor_kontak' => null,
                'nim' => $user->nim,
                'status' => 'terdaftar',
                'terdaftar_pada' => now(),
            ]);
        }, 3);
        $this->resetValidation();
        session()->flash(
            'success',
            'Pendaftaran kegiatan berhasil! Tiket diterbitkan oleh pengurus UKM.'
        );
    }

    public function lihatPeserta(int $id): void
    {
        $user = $this->penggunaAktif();
        // Hanya pengurus UKM aktif yang boleh melihat.
        abort_unless(
            $this->bolehKelola($user),
            403
        );
        // Pastikan kegiatan milik UKM pengurus.
        $kegiatan = Kegiatan::query()
            ->where('ukm_id', $user->ukm_id)
            ->findOrFail($id);
        $this->pesertaKegiatanId = $kegiatan->id;
        $this->tampilkanPeserta = true;
    }

    public function tutupPeserta(): void
    {
        $this->tampilkanPeserta = false;
        $this->pesertaKegiatanId = null;
    }

    public function updatedTampilkanPeserta(bool $value): void
    {
        if (! $value) {
            $this->pesertaKegiatanId = null;
        }
    }

    public function batalkanRegistrasi(int $registrasiId): void
    {
        $user = $this->penggunaAktif();
        DB::transaction(function () use ($registrasiId, $user) {
            // Ambil ID kegiatan dari registrasi.
            $registrasiAwal = RegistrasiKegiatan::query()
                ->findOrFail($registrasiId);
            // Kunci kegiatan terlebih dahulu agar
            // konsisten dengan proses pendaftaran.
            $kegiatan = Kegiatan::query()
                ->lockForUpdate()
                ->findOrFail($registrasiAwal->kegiatan_id);
            $registrasi = RegistrasiKegiatan::query()
                ->lockForUpdate()
                ->findOrFail($registrasiId);
            // Pastikan registrasi masih milik kegiatan itu.
            abort_unless(
                (int) $registrasi->kegiatan_id === (int) $kegiatan->id,
                409
            );
            // Mahasiswa boleh membatalkan miliknya sendiri.
            $pemilik = $this->bolehDaftar($user)
                && (int) $registrasi->user_id === (int) $user->id;
            // Pengurus boleh mengelola peserta UKM sendiri.
            $pengurus = $this->bolehKelola($user)
                && (int) $user->ukm_id === (int) $kegiatan->ukm_id;
            abort_unless($pemilik || $pengurus, 403);
            if ($registrasi->status !== 'terdaftar') {
                $this->addError(
                    'pembatalan',
                    'Registrasi ini sudah dibatalkan.'
                );

                return;
            }
            // Jangan mengganggu tiket atau presensi
            // yang sudah diterbitkan/dicatat.
            if (
                $registrasi->tiketKegiatan()->exists()
                || $registrasi->presensi()->exists()
            ) {
                $this->addError(
                    'pembatalan',
                    'Registrasi tidak bisa dibatalkan karena sudah memiliki tiket atau presensi.'
                );

                return;
            }
            $registrasi->update([
                'status' => 'dibatalkan',
            ]);
            session()->flash(
                'success',
                'Registrasi berhasil dibatalkan.'
            );
        }, 3);
    }

    public function hapusKegiatan(int $id): void
    {
        $user = $this->penggunaAktif();
        abort_unless(
            $this->bolehKelola($user),
            403
        );
        $berhasil = DB::transaction(function () use ($id, $user) {
            // Kunci kegiatan agar tidak diubah selama proses.
            $kegiatan = Kegiatan::query()
                ->where('ukm_id', $user->ukm_id)
                ->lockForUpdate()
                ->findOrFail($id);
            // Periksa seluruh riwayat registrasi,
            // termasuk yang sudah dibatalkan.
            $punyaRegistrasi = $kegiatan
                ->registrasiKegiatan()
                ->exists();
            if ($punyaRegistrasi) {
                return false;
            }
            // Hanya hapus jika belum memiliki registrasi.
            $kegiatan->delete();

            return true;
        }, 3);
        if (! $berhasil) {
            $this->addError(
                'hapus',
                'Kegiatan tidak dapat dihapus karena sudah memiliki riwayat pendaftaran peserta.'
            );

            return;
        }
        // Bersihkan data sementara pada komponen.
        $this->tutupDetail();
        $this->tutupForm();
        $this->resetValidation();
        session()->flash(
            'success',
            'Kegiatan berhasil dihapus!'
        );
    }

    public function render()
    {
        $user = $this->penggunaAktif();
        // Menampilkan Kegiatan UKM
        $query = Kegiatan::query()
            ->with('ukm')
            ->withCount([
                'registrasiKegiatan as jumlah_peserta' => function ($query) {
                    $query->where('status', 'terdaftar');
                },
            ]);
        if (
            $this->bolehKelola($user)
        ) {
            $query->where('ukm_id', $user->ukm_id);
        } else {
            $query->where('status', 'dipublikasikan');
        }
        $detail = null;
        if ($this->detailKegiatanId !== null) {
            $detailQuery = Kegiatan::query()->with('ukm');
            if (
                $this->bolehKelola($user)
            ) {
                $detailQuery->where('ukm_id', $user->ukm_id);
            } else {
                $detailQuery->where('status', 'dipublikasikan');
            }
            $detail = $detailQuery
                ->find($this->detailKegiatanId);
        }
        // Menyiapkan data daftar peserta kegiatan.
        $kegiatanPeserta = null;
        $daftarPeserta = collect();
        $jumlahTerdaftar = 0;
        if (
            $this->pesertaKegiatanId !== null
            && $this->bolehKelola($user)
        ) {
            // Ambil kegiatan milik UKM pengurus.
            $kegiatanPeserta = Kegiatan::query()
                ->where('ukm_id', $user->ukm_id)
                ->find($this->pesertaKegiatanId);
            if ($kegiatanPeserta !== null) {
                // Ambil seluruh registrasi kegiatan tersebut.
                $daftarPeserta = $kegiatanPeserta
                    ->registrasiKegiatan()
                    ->with(['tiketKegiatan', 'presensi'])
                    ->orderByDesc('terdaftar_pada')
                    ->get();
                // Hitung registrasi yang masih aktif.
                $jumlahTerdaftar = $daftarPeserta
                    ->where('status', 'terdaftar')
                    ->count();
            }
        }
        // Daftar registrasi aktif milik mahasiswa login.
        $pendaftaranSaya = [];
        if ($this->bolehDaftar($user)) {
            $pendaftaranSaya = RegistrasiKegiatan::query()
                ->where('user_id', $user->id)
                ->where('status', 'terdaftar')
                ->pluck('id', 'kegiatan_id')
                ->all();
        }

        return view('livewire.kegiatan.index', [
            'kegiatan' => $query
                ->orderByDesc('mulai_pada')
                ->get(),
            'bolehKelola' => $this->bolehKelola($user),
            'detail' => $detail,
            'kegiatanPeserta' => $kegiatanPeserta,
            'daftarPeserta' => $daftarPeserta,
            'jumlahTerdaftar' => $jumlahTerdaftar,
            'bolehDaftar' => $this->bolehDaftar($user),
            'pendaftaranSaya' => $pendaftaranSaya,
        ]);
    }
}
