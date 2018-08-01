## Description
Simple base repositories for Laravel's Eloquent ORM, this packages abstracts the base Model functions and adds a caching decorator.

Caching is handled automatically if you have selected a caching method that allows tagging and cache is enabled in the config.

## Requirements

- `PHP ^7.1.3`
- `Laravel ~5.6`

## Installation

0. As always: back up your database - I am not responsible for any data loss

1. Install the package via Composer:

    `composer require mike-zange/laravel-entity-base-repositories`

2. The service provider is auto-loaded in Laravel >= 5.5

3. Publish the configuration file `repository.php`

    `php artisan vendor:publish --provider="MikeZange\LaravelEntityRepositories\Providers\RepositoryServiceProvider"`

4. Create a contract for your repository:
    ```
    namespace App\Repositories;
    
    use MikeZange\LaravelEntityRepositories\Repositories\BaseRepository;
    
    interface UserRepository extends BaseRepository
    {
    }
    ```

5. Create an Eloquent Repository for your Model:
    ```
    namespace App\Repositories\Eloquent;
    
    use App\Repositories\UserRepository;
    use MikeZange\LaravelEntityRepositories\Repositories\Eloquent\EloquentBaseRepository;
    
    class EloquentUserRepository extends EloquentBaseRepository implements UserRepository
    {
    }
    ```
    
6. Create a Cache Decorator for your Repository:
    ```
    namespace App\Repositories\Cache;
    
    use App\Repositories\UserRepository;
    use MikeZange\LaravelEntityRepositories\Repositories\Cache\CacheBaseDecorator;
    
    class CacheUserDecorator extends CacheBaseDecorator implements UserRepository
    {
        public function __construct(UserRepository $repository)
        {
            parent::__construct();
            $this->entityName = 'user';
            $this->repository = $repository;
        }
    }
    ```

7. Edit the config at `config/repository.php` to your requirements, the defaults should be okay for most uses, but you need to specify your repositories, for example:
    ```
    'repositories' => [
         [
             'entity' => \App\Entities\User::class,
             'contract' => App\Repositories\UserRepository::class,
             'eloquent_repository' => App\Repositories\Eloquent\EloquentUserRepository::class,
             'cache_decorator' => App\Repositories\Cache\CacheUserDecorator::class,
         ]
     ],
     ```
8. You can now type-hint your repositories in your controllers:
    ```
    class UserController extends Controller
    {
        /**
         * @var UserRepository
         */
        private $userRepository;
    
        /**
         * Create a new controller instance.
         *
         * @param UserRepository $userRepository
         */
        public function __construct(UserRepository $userRepository)
        {
            $this->userRepository = $userRepository;
        }
        
        public function index(Request $request)
        {
            $users = $this->userRepository->all();
            return $users;
        }
    }
    ```
