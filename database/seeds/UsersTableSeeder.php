<?php

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 获取 Faker 实例
        $faker = app(Faker\Generator::class);

        // 头像假数据
        $avatars = [
            'https://s2.ax1x.com/2019/11/21/M5K3pd.jpg',
            'https://s2.ax1x.com/2019/11/21/M5Kn0K.jpg',
            'https://s2.ax1x.com/2019/11/21/M5KZOx.jpg',
            'https://s2.ax1x.com/2019/11/21/M5KMkD.jpg'
        ];

        // 生成数据集合
        $users = factory(User::class)
            ->times(10)
            ->make()
            ->each(function ($user, $index) use($faker, $avatars) {
                $user->avatar = $faker->randomElement($avatars);
        });

        // 让隐藏字段可见,并将数据集合转换为数组
        $user_array = $users->makeVisible(['password', 'remember_token'])->toArray();

        User::insert($user_array);

        // 单独处理第一个用户数据
        $user = User::find(1);
        $user->name = 'Xiaoxiao';
        $user->email = 'jixiao35@qq.com';
        $user->avatar = 'https://iocaffcdn.phphub.org/uploads/images/201710/14/1/ZqM7iaP4CR.png';
        $user->save();
    }
}
