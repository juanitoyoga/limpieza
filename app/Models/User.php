<?php

/**
 * Created by Reliese Model.
 */

 namespace App\Models;
	
 // use Illuminate\Contracts\Auth\MustVerifyEmail;
 
 use Illuminate\Database\Eloquent\Factories\HasFactory;
 use Illuminate\Foundation\Auth\User as Authenticatable;
 use Illuminate\Notifications\Notifiable;
 use Illuminate\Support\Facades\Hash;
 use Illuminate\Database\Eloquent\SoftDeletes;
 

 use Laravel\Sanctum\HasApiTokens;
 use Illuminate\Support\Str;
 use Illuminate\Database\Eloquent\Casts\Attribute;
 use App\Models\Userrole;
 
 class User extends Authenticatable
 {
	 use HasFactory,
		 Notifiable,
		 SoftDeletes,
		 HasApiTokens; /** * The attributes that are mass assignable. * * @var array */


/**
 * Class User
 * 
 * @property int $id
 * @property string|null $tipo_id
 * @property string $nro_id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $phone
 * @property Carbon|null $birthdate
 * @property string|null $gender
 * @property string|null $avatar
 * @property string $timezone
 * @property string $language
 * @property Carbon|null $last_login_at
 * @property string|null $last_login_ip
 * @property string|null $verification_token
 * @property bool $is_active
 * @property array|null $marketing_preferences
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property string|null $stripe_id
 * @property string|null $pm_type
 * @property string|null $pm_last_four
 * @property Carbon|null $trial_ends_at
 * @property string|null $remember_token
 * @property string|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Auditor|null $auditor
 * @property Dirigente|null $dirigente
 * @property Funcionario|null $funcionario
 * @property Presidente|null $presidente
 * @property Supervisor|null $supervisor
 * @property Vecino|null $vecino
 *
 * @package App\Models
 */

	use SoftDeletes;
	protected $table = 'users';
	  /** * The attributes that should be cast to native types. * * @var array */

		 /*
		 El accessor getFullNameAttribute() se ejecuta cuando accedes a ->full_name 
		 desde cualquier parte de tu código, gracias a la magia de Eloquent.  
		 */
 

	protected $casts = [
		'email_verified_at' => 'datetime',
		'birthdate' => 'datetime',
		'last_login_at' => 'datetime',
		'is_active' => 'bool',
		'marketing_preferences' => 'json',
		'trial_ends_at' => 'datetime'
	];

	protected $hidden = [
		'password',
		'verification_token',
		'two_factor_secret',
		'remember_token'
	];

	protected $fillable = [
		'tipo_id',
		'nro_id',
		'first_name',
		'last_name',
		'email',
		'email_verified_at',
		'password',
		'phone',
		'birthdate',
		'gender',
		'avatar',
		'timezone',
		'language',
		'last_login_at',
		'last_login_ip',
		'verification_token',
		'is_active',
		'marketing_preferences',
		'two_factor_secret',
		'two_factor_recovery_codes',
		'stripe_id',
		'pm_type',
		'pm_last_four',
		'trial_ends_at',
		'remember_token'
	];




	

		public function getFullNameAttribute()
		{
			return "{$this->first_name} {$this->last_name}";
		}
	
		protected function casts(): array
		{
			return [
				'email_verified_at' => 'datetime',
				'password' => 'hashed',
			];
		}
		/**
		 * Get the user's initials
		 */
		public function initials(): string
		{
			return Str::of($this->name)
				->explode(' ')
				->map(fn (string $name) => Str::of($name)->substr(0, 1))
				->implode('');
		}
		/** * Automatically hash the password when setting it. * * @param string $value * @return void */ 


		protected function password(): Attribute
		{
			return Attribute::make(
				set: fn ($value) => Hash::needsRehash($value)
					? Hash::make($value)
					: $value
			);
		}
				
		/** * Generate a verification token for email confirmation. * * @return void */ 
		public function generateVerificationToken()
		{
			$this->verification_token = Str::random(60);
			$this->save();
		}
	
		/** * Get the URL to the user's avatar. * * @return string */ 
		public function getAvatarUrlAttribute()
		{
			return $this->avatar
				? asset("storage/avatars/{$this->avatar}")
				: asset("images/default-avatar.jpg");
		}

		/** * Get all of the wishlist items for the user. */ 

		public function auditor()
		{
			return $this->hasOne(Auditor::class);
		}
	
		public function dirigente()
		{
			return $this->hasOne(Dirigente::class);
		}
	
		public function funcionario()
		{
			return $this->hasOne(Funcionario::class);
		}
	
		public function presidente()
		{
			return $this->hasOne(Presidente::class);
		}
	
		public function supervisor()
		{
			return $this->hasOne(Supervisor::class);
		}

	    public function userRole()
    	{
        	return $this->hasOne(UserRole::class, 'user_id');
    	}

		public function vecino()
		{
			return $this->hasOne(Vecino::class);
		}

		
}
