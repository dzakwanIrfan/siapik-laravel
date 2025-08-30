<table style="border: 1px solid black; font-family: Arial, sans-serif; color: black; margin: 0 auto;">
    <thead style="border-bottom: 1px solid black;">
        <tr>
            <th>
                <div style="text-align: center; padding: 24px 32px;">
                    <h5 style="font-weight: bold; color: black;">Sistem Informasi Administrasi Pelayanan Akademik</h5>
                    <h5 style="font-weight: bold; color: black;">(SIAPIK)</h5>
                    <h5 style="font-weight: bold; color: black;">Pascasarjana Universitas Halu Oleo</h5>
                </div>
            </th>
        </tr>
    </thead>
    <tbody style="border-bottom: 1px solid black;">
        <tr>
            <td>
                <div style="text-align: center; padding: 24px 32px;">
                    <h3 style="text-decoration: underline; color: black;">BUKTI PENGAJUAN</h3>
                    <div style="font-size: 20px">{{ $submission->letterType->txtNameLetterType }}</div>
                    <table style="margin: 16px auto;">
                        <tr>
                            <td style="text-align: left; padding-right: 32px;">Tanggal Pengajuan</td>
                            <td style="text-align: left;">: {{ $submission->dtmInserted }}</td>
                        </tr>
                        <tr>
                            <td style="text-align: left;">NIM</td>
                            <td style="text-align: left;">: {{ $submission->user->mahasiswaProfile->txtNIM }}</td>
                        </tr>
                        <tr>
                            <td style="text-align: left;">Nama</td>
                            <td style="text-align: left;">: {{ $submission->user->txtFullName }}</td>
                        </tr>
                        <tr>
                            <td style="text-align: left;">Prodi</td>
                            <td style="text-align: left;">: {{ $submission->user->mahasiswaProfile->major->txtNameMajor }}</td>
                        </tr>
                        <tr>
                            <td style="text-align: left;">Keterangan</td>
                            <td style="text-align: left;">:</td>
                        </tr>
                    </table>
                    <ol type="1" style="text-align: left; margin: 0 auto; width: 31rem">
                        <li>Bukti Pengajuan dibawah serta pada saat pengambilan surat</li>
                        <li>Proses surat 3 hari kerja (Kecuali Surat Cuti menunggu pengesahan dari Rektorat)</li>
                        <li>Dokumen persyaratan asli dibawa serta saat pengambilan surat</li>
                        <li>Cek Secara berkala Riwayat Pengajuan pada Sistem Siapik</li>
                    </ol>
                </div>
            </td>
        </tr>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="2" style="text-align: center; padding: 12px 0;">
                <h5 style="color: black; font-weight: 400">Nomor Receipt: <span style="font-weight: bold;">#{{ $submission->txtReceiptNumber }}</span></h5>
            </td>
        </tr>
    </tfoot>
</table>