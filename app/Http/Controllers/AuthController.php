<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Barrio;
use App\Models\User;
use App\Models\Vecino;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Login para la App Móvil (Sanctum)
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email'       => 'required|email',
            'password'    => 'required',
            'device_name' => 'nullable|string',
        ], [
            'email.required'    => 'El correo electrónico es obligatorio.',
            'email.email'       => 'El correo electrónico no es válido.',
            'password.required' => 'La contraseña es obligatoria.',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Las credenciales son incorrectas.',
                'errors'  => ['email' => ['Correo o contraseña incorrectos.']],
            ], 401);
        }

        $user->load('vecino.barrio');
        $token = $user->createToken($request->device_name ?? 'android_device')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Bienvenido al sistema LimpiaTuRincón.',
            'token'   => $token,
            'user'    => $user,
        ]);
    }

    /**
     * Registro de nuevos Vecinos desde la App Móvil
     */
    public function register(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'tipo_id'    => 'required|string|in:CÉDULA,PASAPORTE,RUC|max:25',
                'nro_id'     => 'required|string|max:25|unique:users,nro_id',
                'cedula'     => 'required|string|max:25|unique:vecinos,cedula',
                'first_name' => 'required|string|max:255',
                'last_name'  => 'required|string|max:255',
                'email'      => 'required|string|email|max:255|unique:users,email',
                'password'   => 'required|string|min:8',
                'phone'      => 'nullable|string|max:20',
                'gender'     => 'nullable|string|in:MASCULINO,FEMENINO,OTRO',
                'birthdate'  => 'nullable|date',
                'barrio_id'  => 'required|integer|exists:barrios,id',
            ], [
                // tipo_id
                'tipo_id.required' => 'El tipo de documento es obligatorio.',
                'tipo_id.in'       => 'El tipo de documento debe ser CÉDULA, PASAPORTE o RUC.',

                // nro_id
                'nro_id.required'  => 'El número de identificación es obligatorio.',
                'nro_id.max'       => 'El número de identificación no debe superar 25 caracteres.',
                'nro_id.unique'    => 'Este número de identificación ya está registrado.',

                // cedula
                'cedula.required'  => 'La cédula es obligatoria.',
                'cedula.unique'    => 'Esta cédula ya está registrada en el sistema.',

                // nombres
                'first_name.required' => 'Los nombres son obligatorios.',
                'last_name.required'  => 'Los apellidos son obligatorios.',

                // email
                'email.required'   => 'El correo electrónico es obligatorio.',
                'email.email'      => 'El correo electrónico no tiene un formato válido.',
                'email.unique'     => 'Este correo electrónico ya está registrado.',

                // password
                'password.required' => 'La contraseña es obligatoria.',
                'password.min'      => 'La contraseña debe tener al menos 8 caracteres.',

                // género
                'gender.in'        => 'El género debe ser MASCULINO, FEMENINO u OTRO.',

                // fecha
                'birthdate.date'   => 'La fecha de nacimiento no es válida.',

                // barrio
                'barrio_id.required' => 'Debe seleccionar un barrio.',
                'barrio_id.exists'   => 'El barrio seleccionado no existe.',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Por favor revisa los datos ingresados.',
                'errors'  => $e->errors(),
            ], 422);
        }

        try {
            $result = DB::transaction(function () use ($validated) {
                // 1. Obtener id_DMQ del barrio seleccionado
                $barrio = Barrio::findOrFail($validated['barrio_id']);
                // Accedemos al género desde el array de validados
                $genderInput = trim($validated['gender'] ?? 'OTRO');

                $finalGender = "Otro";
                if (strtoupper($genderInput) === 'MASCULINO' || strtoupper($genderInput) === 'M') {
                    $finalGender = 'M';
                } elseif (strtoupper($genderInput) === 'FEMENINO' || strtoupper($genderInput) === 'F') {
                    $finalGender = 'F';
                }

                // 2. Crear el Usuario
                $user = User::create([
                    'tipo_id'    => $validated['tipo_id'],
                    'nro_id'     => $validated['nro_id'],
                    'first_name' => $validated['first_name'],
                    'last_name'  => $validated['last_name'],
                    'email'      => $validated['email'],
                    'password'   => $validated['password'], // mutator lo hashea
                    'phone'      => $validated['phone']     ?? null,
                    'gender'     => $finalGender,
                    'birthdate'  => $validated['birthdate'] ?? null,
                    'role_name'  => 'Vecino',
                    'is_active'  => true,
                ]);

                // 3. Crear el Vecino con id_DMQ del barrio
                $user->vecino()->create([
                    'cedula'    => $validated['cedula'],
                    'id_DMQ'    => $barrio->id_DMQ,
                    'barrio_id' => $validated['barrio_id'],
                ]);

                // 4. Generar token Sanctum
                $token = $user->createToken('android_device')->plainTextToken;

                return [
                    'token' => $token,
                    'user'  => $user->load('vecino.barrio'),
                ];
            });

            return response()->json([
                'success' => true,
                'message' => '¡Registro exitoso! Bienvenido a LimpiaTuRincón.',
                'token'   => $result['token'],
                'user'    => $result['user'],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al registrar. Intenta nuevamente.',
                'errors'  => ['server' => [$e->getMessage()]],
            ], 500);
        }
    }
}
