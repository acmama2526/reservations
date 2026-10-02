<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSnapshotSeeder extends Seeder
{
    public function run(): void
    {
        $users = array (
  0 => 
  array (
    'name' => 'ちいかわ',
    'email' => 'chiikawa@test.com',
    'password' => '$2y$12$1HfD11vX0mJBBzO2cNQqf.6wZUfGGNWX6XqTCb/aO5pQh3.AyM/aK',
    'role' => 'staff',
  ),
  1 => 
  array (
    'name' => 'ハチワレ',
    'email' => 'hachiware@test.com',
    'password' => '$2y$12$42e/U9yfp4E7soJfEp9l0eXeoubkRXAjQGtdXUa/MIqA.aHeWvRWK',
    'role' => 'admin',
  ),
  2 => 
  array (
    'name' => 'うさぎ',
    'email' => 'usagi@test.com',
    'password' => '$2y$12$nPdHwo5BwX5rAdX8K8sdC.mm02QYckyJSLNsPiktkBhOvG0CXrPI2',
    'role' => 'manager',
  ),
  3 => 
  array (
    'name' => 'モモンガ',
    'email' => 'momonga@test.com',
    'password' => '$2y$12$RMBtGqNGhONjiUxvCeTzS.shAsxZPTTOLWIE7kAi8/B4EqF1o/iCq',
    'role' => 'staff',
  ),
  4 => 
  array (
    'name' => 'くりまんじゅう',
    'email' => 'kurimanjyuu@test.com',
    'password' => '$2y$12$gvPdzPtFSXUEctbOkfe6XOACyBJ5T5etZNiGlKENKBfqeyRK8hqQC',
    'role' => 'staff',
  ),
  5 => 
  array (
    'name' => 'ラッコ',
    'email' => 'rakko@test.com',
    'password' => '$2y$12$..kC.XWAE2BlpNf9jQlaEe1UyEeovtFi3LzzKEIRdLIDpLeq4e7AK',
    'role' => 'manager',
  ),
  6 => 
  array (
    'name' => 'シーサー',
    'email' => 'sa-sa-@test.com',
    'password' => '$2y$12$DoautmTTOLo6VoQ8svwv.esOvAIPmcZnXZyZyKzJ2/qf/StOcZp02',
    'role' => 'manager',
  ),
  7 => 
  array (
    'name' => 'カニちゃん',
    'email' => 'kanichann@test.com',
    'password' => '$2y$12$9HWP.HGgtNHSwKeKGqAQB.uoaS4R5v4LcgUbkae6Gkn4h0FCIKSvG',
    'role' => 'staff',
  ),
  8 => 
  array (
    'name' => '鎧さん',
    'email' => 'yoroi@test.com',
    'password' => '$2y$12$Uvyzf0XF.N.cGsxtBnPKKe.iFjKtuaK1EKEDxSvKSAd.GwHId1i0C',
    'role' => 'admin',
  ),
  9 => 
  array (
    'name' => 'キメラ',
    'email' => 'kimera@test.com',
    'password' => '$2y$12$qGFGpBDYfJCe1q22iA8OXOGlUa1.XuYWUs.3xSf4e4cHywzHQlACS',
    'role' => 'staff',
  ),
  10 => 
  array (
    'name' => 'あのこ',
    'email' => 'anoko@test.com',
    'password' => '$2y$12$JCmCv30XVTPH2lf5pp8EJOwwBsnAolIY0cTY7vaDSTwebO74OpCWW',
    'role' => 'staff',
  ),
  11 => 
  array (
    'name' => 'でかつよ',
    'email' => 'dekatsuyo@test.com',
    'password' => '$2y$12$m.8GzUo3UqTzOa/zlIpvPOtgOTr68.cH5NCocCwZWlo42dNYEjEeq',
    'role' => 'staff',
  ),
  12 => 
  array (
    'name' => 'お面キメラ',
    'email' => 'omennkimera@test.com',
    'password' => '$2y$12$lB1pE0ZlqsUNpRGm22TgRuzwcPi.Lt2g64xnJtcvGZSkB7VPCMo3.',
    'role' => 'staff',
  ),
  13 => 
  array (
    'name' => 'スフィンクス',
    'email' => 'sufinnkusu@test.com',
    'password' => '$2y$12$teVDw52Hoo4VgkliIrSYGeGIH5em1TKrZvsFQbahszMw/u8eeGLYa',
    'role' => 'staff',
  ),
  14 => 
  array (
    'name' => '流れ星',
    'email' => 'nagareboshi@test.com',
    'password' => '$2y$12$/AxM4fAY3n75nHrCRztPI.hGEepqdiahoK56s8QVPqaY.xIKRsL4i',
    'role' => 'staff',
  ),
  15 => 
  array (
    'name' => 'ゴブリン',
    'email' => 'goburinn@test.com',
    'password' => '$2y$12$AqP9PUpY9wdEH5kgCY0zouRrLHU3Xq.MQVyQtmtAxO2ChMYtpoqNu',
    'role' => 'staff',
  ),
  16 => 
  array (
    'name' => 'オデ',
    'email' => 'ode@tesu.com',
    'password' => '$2y$12$XQimnMRvXu6Du904od3wCew3RNFpCvYN/zmju5aRzr4HykBavta1m',
    'role' => 'staff',
  ),
  17 => 
  array (
    'name' => 'セイレーン',
    'email' => 'seire-nn@test.com',
    'password' => '$2y$12$01vNvXgk7O2ZXbE9CcNT2eZroAw8c1ZYHKWPjXkivxG654H.TqPe6',
    'role' => 'staff',
  ),
  18 => 
  array (
    'name' => '島二郎',
    'email' => 'shimajirou@test.com',
    'password' => '$2y$12$M97lA2wnIbwgz3aVScxK4ef3OZcsQwa4mPA0eoLKM8h7VxQMAyMx2',
    'role' => 'manager',
  ),
  19 => 
  array (
    'name' => 'マンボウ',
    'email' => 'mannbou@test.com',
    'password' => '$2y$12$vwlvS/mXszFhLF42KUId..G5BSZ0yF5ky7/BQVCl7kvuFrvguXksa',
    'role' => 'staff',
  ),
  20 => 
  array (
    'name' => 'けん玉おじさん',
    'email' => 'kenndamaojisann@test.com',
    'password' => '$2y$12$3DS34y9NNIXSUpYOrUAO..QzAcoTU5F6r6hXSppaxde38f2vmsDcu',
    'role' => 'staff',
  ),
  21 => 
  array (
    'name' => '中止で～す',
    'email' => 'tyuuside-su@test.com',
    'password' => '$2y$12$eg2oEobuLY8gX3EE49f1eeDRW0hLaIiFzUsdL4uTFoqID2d8nz8fG',
    'role' => 'staff',
  ),
  22 => 
  array (
    'name' => '網脂',
    'email' => 'amiabura@test.com',
    'password' => '$2y$12$EPjxfIkljDzqpWSlqfwRKupUvmr4qFAWAoaUX8ypzC4iC3r9IVaRS',
    'role' => 'staff',
  ),
  23 => 
  array (
    'name' => 'アリジゴク',
    'email' => 'arijigoku@test.com',
    'password' => '$2y$12$olh5i8UtE.v.F8U4856nVe6SCYEn0TRw03HZa6BQrdU3o4RN8dHcC',
    'role' => 'staff',
  ),
  24 => 
  array (
    'name' => '木のおじさん',
    'email' => 'kinoojisann@test.com',
    'password' => '$2y$12$7ziB53k3R.u.8qXGrMpdvuUkUNQVCvWQs6WNjMXdib5uWsGNazaNG',
    'role' => 'staff',
  ),
  25 => 
  array (
    'name' => 'むちゃうマン',
    'email' => 'muchaumann@test.com',
    'password' => '$2y$12$In7ZfeB5wS26hdjba9He0OySOj7c1ony4VQvBP30K90X07RtkXp1q',
    'role' => 'staff',
  ),
);

        foreach ($users as $user) {
            // 同じメールアドレスのユーザーがいれば変更しない
            if (DB::table('users')->where('email', $user['email'])->exists()) {
                continue;
            }

            DB::table('users')->insert([
                'name' => $user['name'],
                'email' => $user['email'],
                'password' => $user['password'],
                'role' => $user['role'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}