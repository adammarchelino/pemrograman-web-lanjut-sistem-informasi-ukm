<div class="p-6 space-y-6">

    <div class="flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold">
                Manajemen Kegiatan UKM
            </h1>
            <p class="text-sm text-gray-500 mt-1">
                Kelola kegiatan Unit Kegiatan Mahasiswa
            </p>
        </div>

        @if ($bolehKelola)
        <button
            type="button"
            wire:click="bukaForm"
            class="rounded-lg bg-blue-600 px-4 py-2
                       text-white hover:bg-blue-700">
            + Tambah Kegiatan
        </button>
        @endif
    </div>

    @if (session()->has('success'))
    <div class="rounded-lg bg-green-100 p-4
                    text-green-800">
        {{ session('success') }}
    </div>
    @endif

    @if ($tampilkanForm && $bolehKelola)
    <div class="rounded-xl border p-6">
        <h2 class="text-xl font-semibold mb-5">
            {{ $editKegiatanId ? 'Form Edit Kegiatan' : 'Form Tambah Kegiatan' }}
        </h2>

        <form wire:submit="simpan" class="space-y-4">

            <div>
                <label class="block mb-1">
                    Nama Kegiatan
                </label>
                <input
                    type="text"
                    wire:model="nama"
                    class="w-full rounded-lg border p-2"
                    placeholder="Masukkan nama kegiatan">
                @error('nama')
                <p class="text-red-500 text-sm">
                    {{ $message }}
                </p>
                @enderror
            </div>

            <div>
                <label class="block mb-1">
                    Deskripsi
                </label>
                <textarea
                    wire:model="deskripsi"
                    rows="3"
                    class="w-full rounded-lg border p-2"
                    placeholder="Deskripsi kegiatan"></textarea>
                @error('deskripsi')
                <p class="text-red-500 text-sm">
                    {{ $message }}
                </p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block mb-1">
                        Tanggal Mulai
                    </label>
                    <input
                        type="datetime-local"
                        wire:model="mulai_pada"
                        class="w-full rounded-lg border p-2">
                    @error('mulai_pada')
                    <p class="text-red-500 text-sm">
                        {{ $message }}
                    </p>
                    @enderror
                </div>

                <div>
                    <label class="block mb-1">
                        Tanggal Selesai
                    </label>
                    <input
                        type="datetime-local"
                        wire:model="selesai_pada"
                        class="w-full rounded-lg border p-2">
                    @error('selesai_pada')
                    <p class="text-red-500 text-sm">
                        {{ $message }}
                    </p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="block mb-1">Lokasi</label>
                <input
                    type="text"
                    wire:model="lokasi"
                    class="w-full rounded-lg border p-2"
                    placeholder="Lokasi kegiatan">
                @error('lokasi')
                <p class="text-red-500 text-sm">
                    {{ $message }}
                </p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block mb-1">
                        Kapasitas Peserta
                    </label>
                    <input
                        type="number"
                        min="1"
                        wire:model="kapasitas"
                        class="w-full rounded-lg border p-2">
                    @error('kapasitas')
                    <p class="text-red-500 text-sm">
                        {{ $message }}
                    </p>
                    @enderror
                </div>

                <div>
                    <label class="block mb-1">
                        Audiens
                    </label>
                    <select
                        wire:model="audiens"
                        class="w-full rounded-lg border p-2">
                        <option value="mahasiswa_poliwangi">
                            Mahasiswa Poliwangi
                        </option>
                        <option value="umum">
                            Umum
                        </option>
                    </select>
                    @error('audiens')
                    <p class="text-red-500 text-sm">
                        {{ $message }}
                    </p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block mb-1">
                        Pendaftaran Dibuka
                    </label>
                    <input
                        type="datetime-local"
                        wire:model="pendaftaran_dibuka_pada"
                        class="w-full rounded-lg border p-2">
                    @error('pendaftaran_dibuka_pada')
                    <p class="text-red-500 text-sm">
                        {{ $message }}
                    </p>
                    @enderror
                </div>

                <div>
                    <label class="block mb-1">
                        Pendaftaran Ditutup
                    </label>
                    <input
                        type="datetime-local"
                        wire:model="pendaftaran_ditutup_pada"
                        class="w-full rounded-lg border p-2">
                    @error('pendaftaran_ditutup_pada')
                    <p class="text-red-500 text-sm">
                        {{ $message }}
                    </p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="block mb-1">
                    Status Publikasi
                </label>
                <select
                    wire:model="status"
                    class="w-full rounded-lg border p-2">
                    <option value="draf">Draf</option>
                    <option value="dipublikasikan">
                        Dipublikasikan
                    </option>
                </select>
                @error('status')
                <p class="text-red-500 text-sm">
                    {{ $message }}
                </p>
                @enderror
            </div>

            <div class="flex gap-3 pt-4">
                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    wire:target="simpan"
                    class="rounded-lg bg-green-600
                               px-5 py-2 text-white
                               hover:bg-green-700
                               disabled:opacity-50">
                    {{ $editKegiatanId ? 'Simpan Perubahan' : 'Simpan Kegiatan' }}
                </button>

                <button
                    type="button"
                    wire:click="tutupForm"
                    class="rounded-lg border px-5 py-2">
                    Batal
                </button>
            </div>
        </form>
    </div>
    @endif

    <div class="overflow-x-auto rounded-xl border">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-100 text-gray-700">
                <tr>
                    <th class="p-3">No</th>
                    <th class="p-3">Nama Kegiatan</th>
                    <th class="p-3">UKM</th>
                    <th class="p-3">Tanggal</th>
                    <th class="p-3">Lokasi</th>
                    <th class="p-3">Peserta</th>
                    <th class="p-3">Status</th>
                    <th class="p-3">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($kegiatan as $item)
                <tr class="border-t">
                    <td class="p-3">
                        {{ $loop->iteration }}
                    </td>
                    <td class="p-3 font-medium">
                        {{ $item->nama }}
                    </td>
                    <td class="p-3">
                        {{ $item->ukm->nama }}
                    </td>
                    <td class="p-3">
                        {{ $item->mulai_pada->format('d M Y H:i') }}
                    </td>
                    <td class="p-3">
                        {{ $item->lokasi }}
                    </td>
                    <td class="p-3">
                        {{ $item->jumlah_peserta }}
                        /
                        {{ $item->kapasitas }}
                    </td>
                    <td class="p-3">
                        {{ ucfirst($item->status) }}
                    </td>

                    <td class="p-3">
                        <div class="flex items-center gap-2">

                            <button
                                type="button"
                                wire:click="lihatDetail({{ $item->id }})"
                                class="rounded-lg bg-blue-600
                   px-3 py-2 text-white
                   hover:bg-blue-700">
                                Detail
                            </button>

                            @if ($bolehKelola)
                            <button
                                type="button"
                                wire:click="editKegiatan({{ $item->id }})"
                                class="rounded-lg bg-amber-500
                       px-3 py-2 text-white
                       hover:bg-amber-600">
                                Edit
                            </button>
                            @endif

                        </div>
                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="8"
                        class="p-6 text-center text-gray-500">
                        Belum ada kegiatan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>


        @if ($detail)
        <div class="fixed inset-0 z-50 flex items-center
                justify-center bg-black/70 p-4">

            <div class="w-full max-w-2xl max-h-[90vh]
                    overflow-y-auto rounded-xl
                    bg-white p-6 text-gray-900 shadow-xl">

                <div class="mb-5 flex items-center
                        justify-between gap-3">
                    <h2 class="text-xl font-bold">
                        Detail Kegiatan
                    </h2>

                    <button
                        type="button"
                        wire:click="tutupDetail"
                        class="rounded-lg border px-3 py-1">
                        Tutup
                    </button>
                </div>

                <div class="space-y-4">

                    <div>
                        <p class="text-sm text-gray-500">
                            Nama Kegiatan
                        </p>
                        <p class="font-semibold">
                            {{ $detail->nama }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">UKM</p>
                        <p>{{ $detail->ukm->nama ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">
                            Deskripsi
                        </p>
                        <p class="whitespace-pre-line">
                            {{ $detail->deskripsi }}
                        </p>
                    </div>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <p class="text-sm text-gray-500">
                                Tanggal Mulai
                            </p>
                            <p>
                                {{ $detail->mulai_pada?->format('d M Y H:i') }}
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">
                                Tanggal Selesai
                            </p>
                            <p>
                                {{ $detail->selesai_pada?->format('d M Y H:i') }}
                            </p>
                        </div>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">
                            Lokasi
                        </p>
                        <p>{{ $detail->lokasi }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">
                            Kapasitas Peserta
                        </p>
                        <p>{{ $detail->kapasitas }} orang</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">
                            Audiens
                        </p>
                        <p>{{ str_replace('_', ' ', ucfirst($detail->audiens)) }}</p>
                    </div>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <p class="text-sm text-gray-500">
                                Pendaftaran Dibuka
                            </p>
                            <p>
                                {{ $detail->pendaftaran_dibuka_pada?->format('d M Y H:i') ?? '-' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">
                                Pendaftaran Ditutup
                            </p>
                            <p>
                                {{ $detail->pendaftaran_ditutup_pada?->format('d M Y H:i') ?? '-' }}
                            </p>
                        </div>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">
                            Status Publikasi
                        </p>
                        <p class="font-semibold">
                            {{ ucfirst($detail->status) }}
                        </p>
                    </div>

                </div>

                <div class="mt-6 flex justify-end border-t pt-4">
                    <button
                        type="button"
                        wire:click="tutupDetail"
                        class="rounded-lg bg-gray-800 px-5
                           py-2 text-white hover:bg-gray-700">
                        Kembali
                    </button>
                </div>

            </div>
        </div>
        @endif

    </div>
</div>