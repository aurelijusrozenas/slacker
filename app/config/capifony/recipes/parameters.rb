#
# Upload parameters-<env>.yml file, if it exists
#
set :parameters_dir, "app/config/capifony/"

namespace :parameters do
    desc 'Uploads parameters-<environment>.yml file'
    task :upload do
        set :origin_file, parameters_dir + "parameters.yml-server"
        if origin_file && File.exists?(origin_file)
            relative_path = "app/config/parameters.yml"

            if shared_files && shared_files.include?(relative_path)
                destination_file = shared_path + "/" + relative_path
            else
                destination_file = latest_release + "/" + relative_path
            end
            try_sudo "mkdir -p #{File.dirname(destination_file)}"

            try_sudo "cp #{destination_file} #{destination_file}.backup.$$ || true"

            top.upload(origin_file, destination_file)

            puts "Uploaded config #{origin_file.yellow}. Old file saved to " + "#{destination_file}.backup".yellow + "."
        else
            puts "Uploaded failed. File #{origin_file} does not exist.".red
        end
    end

    desc 'Downloads the current parameters.yml file'
    task :download do
        set :filename, "#{parameters_dir}parameters.yml-server"
        top.download("#{shared_path}/app/config/parameters.yml", filename, :via => :scp)
        puts "Config downloaded to #{filename.yellow}"
    end
end
