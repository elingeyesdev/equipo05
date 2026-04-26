<?php $__env->startSection('adminlte_css_pre'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('vendor/icheck-bootstrap/icheck-bootstrap.min.css')); ?>">
<?php $__env->stopSection(); ?>

<?php
    $loginUrl = View::getSection('login_url') ?? config('adminlte.login_url', 'login');
    $registerUrl = View::getSection('register_url') ?? config('adminlte.register_url', 'register');
    $passResetUrl = View::getSection('password_reset_url') ?? config('adminlte.password_reset_url', 'password/reset');

    if (config('adminlte.use_route_url', false)) {
        $loginUrl = $loginUrl ? route($loginUrl) : '';
        $registerUrl = $registerUrl ? route($registerUrl) : '';
        $passResetUrl = $passResetUrl ? route($passResetUrl) : '';
    } else {
        $loginUrl = $loginUrl ? url($loginUrl) : '';
        $registerUrl = $registerUrl ? url($registerUrl) : '';
        $passResetUrl = $passResetUrl ? url($passResetUrl) : '';
    }
?>

<?php $__env->startSection('auth_header', __('adminlte::adminlte.login_message')); ?>

<?php $__env->startSection('auth_body'); ?>
<form method="post" action="<?php echo e($loginUrl); ?>">
    <?php echo csrf_field(); ?>
    
    <!-- Email input -->
    <div class="input-group mb-3">
        <input type="email" name="email" class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
               placeholder="<?php echo e(__('adminlte::adminlte.email')); ?>" value="<?php echo e(old('email')); ?>" autofocus required>
        <div class="input-group-append">
            <div class="input-group-text">
                <span class="fas fa-envelope"></span>
            </div>
        </div>
        <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <span class="invalid-feedback" role="alert">
                <strong><?php echo e($message); ?></strong>
            </span>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    
    <!-- Password input -->
    <div class="input-group mb-3">
        <input type="password" name="password" class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
               placeholder="<?php echo e(__('adminlte::adminlte.password')); ?>" required>
        <div class="input-group-append">
            <div class="input-group-text">
                <span class="fas fa-lock"></span>
            </div>
        </div>
        <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <span class="invalid-feedback" role="alert">
                <strong><?php echo e($message); ?></strong>
            </span>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    
    <!-- Remember me -->
    <div class="row">
        <div class="col-7">
            <div class="icheck-primary" title="<?php echo e(__('adminlte::adminlte.remember_me_hint')); ?>">
                <input type="checkbox" id="remember" name="remember" <?php echo e(old('remember') ? 'checked' : ''); ?>>
                <label for="remember">
                    <?php echo e(__('adminlte::adminlte.remember_me')); ?>

                </label>
            </div>
        </div>
        <div class="col-5">
            <button type="submit" class="btn btn-block <?php echo e(config('adminlte.classes_auth_btn', 'btn-flat btn-primary')); ?>">
                <span class="fas fa-sign-in-alt"></span>
                <?php echo e(__('adminlte::adminlte.sign_in')); ?>

            </button>
        </div>
    </div>
</form>

<!-- Google OAuth Button -->
<div class="social-auth-links text-center mt-3">
    <p class="text-muted">- O -</p>
    <a href="<?php echo e(route('google.redirect')); ?>" class="btn btn-danger btn-block">
        <i class="fab fa-google mr-2"></i> Iniciar sesión con Google
    </a>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('auth_footer'); ?>
    
    <?php if($passResetUrl): ?>
        <p class="my-0">
            <a href="<?php echo e($passResetUrl); ?>">
                <?php echo e(__('adminlte::adminlte.i_forgot_my_password')); ?>

            </a>
        </p>
    <?php endif; ?>

    
    <?php if($registerUrl): ?>
        <p class="my-0">
            <a href="<?php echo e($registerUrl); ?>">
                <?php echo e(__('adminlte::adminlte.register_a_new_membership')); ?>

            </a>
        </p>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('adminlte::auth.auth-page', ['authType' => 'login'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\lenovo\OneDrive\Desktop\Proyectos\SIPII Laravel\Laraprueba-CRUD\Laraprueba-CRUD\resources\views/auth/login.blade.php ENDPATH**/ ?>