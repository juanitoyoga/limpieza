<?php
/**
 * Created by Reliese Model.
 */

 namespace App\Models;

 use Carbon\Carbon;
 use Illuminate\Database\Eloquent\Collection;
 use Illuminate\Database\Eloquent\Model;
 use App\Models\MenuItem;
 use App\Models\Userrole;
 
 /**
  * Class Role
  * 
  * @property int $id
  * @property string $name
  * @property string $guard_name
  * @property Carbon|null $created_at
  * @property Carbon|null $updated_at
  * 
  * @property Collection|MenuItem[] $menuItems
  *
  * @package App\Models
  */
 class Role extends Model
 {
	 protected $table = 'roles';
 
	 protected $fillable = [
		 'name',
		 'guard_name'
	 ];
 
	 public function menuItems()
	 {
		 return $this->hasMany(MenuItem::class);
	 }
 
	 public function scopeOrdered($query)
	 {
		 return $query->orderBy('name');
	 }
 
	 public function scopeGuard($query, $guardName)
	 {
		 return $query->where('guard_name', $guardName);
	 }
 
	 public function isAdmin()
	 {
		 return $this->name === 'admin';
	 }
 
	 public function hasMenuItems()
	 {
		 return $this->menuItems()->count() > 0;
	 }

	 public function userRoles()
	 {
		 return $this->hasMany(UserRole::class, 'role_id');
	 }
 }