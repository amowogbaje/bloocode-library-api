<?php
namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => Hash::make('password'), 
            'role' => $this->faker->randomElement(['Admin', 'Librarian', 'Member']),
        ];
    }

    /**
     * Indicate that the user is an admin.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function admin()
    {
        return $this->state([
            'role' => 'Admin',
        ]);
    }

    /**
     * Indicate that the user is a librarian.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function librarian()
    {
        return $this->state([
            'role' => 'Librarian',
        ]);
    }

    /**
     * Indicate that the user is a member.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function member()
    {
        return $this->state([
            'role' => 'Member',
        ]);
    }
}
