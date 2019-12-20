# config valid for current version and patch releases of Capistrano
lock "~> 3.11.2"

set :application, "resources"
set :repo_url, "git@github.com:polvanovvgosh/wp-site.git"

# Default branch is :master
# ask :branch, `git rev-parse --abbrev-ref HEAD`.chomp

# Default deploy_to directory is /var/www/my_app_name
set :deploy_to, "/var/www/html/resources"

# Default value for :format is :airbrussh.
# set :format, :airbrussh

# You can configure the Airbrussh format using :format_options.
# These are the defaults.
# set :format_options, command_output: true, log_file: "log/capistrano.log", color: :auto, truncate: :auto

# Default value for :pty is false
# set :pty, true

# Default value for :linked_files is []
# append :linked_files, "config/database.yml"

# Default value for linked_dirs is []
# append :linked_dirs, "log", "tmp/pids", "tmp/cache", "tmp/sockets", "public/system"

# Default value for default_env is {}
# set :default_env, { path: "/opt/ruby/bin:$PATH" }

# Default value for local_user is ENV['USER']
# set :local_user, -> { `git config user.name`.chomp }

# Default value for keep_releases is 5
set :keep_releases, 3

# Uncomment the following to require manually verifying the host key before first deploy.
set :ssh_options, verify_host_key: :secure

#Create config links
namespace :deploy do
    task :create_config_links do
     on roles :all do
        execute :ln, "-s  #{shared_path}/.htaccess #{release_path}/.htaccess"
        execute :ln, "-s  #{shared_path}/wp-config.php #{release_path}/wp-config.php"
        execute :ln, "-s  #{shared_path}/uploads #{release_path}/wp-content/uploads"
        end
    end
end

before "deploy:finished", "deploy:create_config_links"