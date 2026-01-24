<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    protected string $role_name = 'User';

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     * @throws ValidationException
     */
    public function create(array $input): User
    {
        try {

            // 1️⃣ Validación
            Validator::make($input, [
                'tipo_id'     => ['required', 'string', 'max:50'],
                'nro_id'      => ['required', 'string', 'max:50', 'unique:users,nro_id'],
                'first_name'  => ['required', 'string', 'max:255'],
                'last_name'   => ['required', 'string', 'max:255'],
                'email'       => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
                'phone'       => ['nullable', 'string', 'max:20'],
                'gender'      => ['nullable', 'string', 'max:10'],
                'birthdate'   => ['nullable', 'date'],
                'password'    => ['required', 'string', 'confirmed', 'min:8'],
            ])->validate();

            // 2️⃣ Creación del usuario
            return User::create([
                'tipo_id'    => $input['tipo_id'],
                'nro_id'     => $input['nro_id'],
                'first_name' => $input['first_name'],
                'last_name'  => $input['last_name'],
                'email'      => $input['email'],
                'phone'      => $input['phone'] ?? null,
                'gender'     => $input['gender'] ?? null,
                'birthdate'  => $input['birthdate'] ?? null,
                'password'   => Hash::make($input['password']),
                'role_name'  => $this->role_name,
                'is_active'  => true,
            ]);

        } catch (ValidationException $e) {
            // 🔴 Errores de validación (Fortify los maneja automáticamente)
            throw $e;

        } catch (QueryException $e) {
            // 🔴 Errores de base de datos (unique, constraints, etc.)
            Log::error('Error DB al crear usuario', [
                'error' => $e->getMessage(),
                'input' => collect($input)->except('password')->toArray(),
            ]);

            throw ValidationException::withMessages([
                'email' => 'Ocurrió un problema al registrar el usuario. Verifique los datos.',
            ]);

        } catch (\Throwable $e) {
            // 🔴 Error inesperado
            Log::critical('Error inesperado al crear usuario', [
                'exception' => $e,
            ]);

            throw ValidationException::withMessages([
                'general' => 'Ocurrió un error inesperado. Intente nuevamente.',
            ]);
        }
    }
}
