VAGRANTFILE_API_VERSION = "2"
Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
	# Every Vagrant virtual environment requires a box to build off of.
	# config.vm.box = "covex/symfony-ubuntu1204-x64"
	# config.vm.box_url = "http://files.vagrantup.com/precise64.box"
	config.vm.box = "precise64"
	config.vm.box_url = "http://files.vagrantup.com/precise64.box"

	config.vm.provider "virtualbox" do |v|
		# Fix DNS, see http://serverfault.com/a/496612
		v.customize ["modifyvm", :id, "--natdnshostresolver1", "on"]
		v.customize ["modifyvm", :id, "--memory", 2048]
        v.customize ["setextradata", :id, "VBoxInternal2/SharedFoldersEnableSymlinksCreate/v-root", "1"]
	end

	# Forward a port from the guest to the host, which allows for outside
	# computers to access the VM, whereas host only networking does not.
	config.vm.network "private_network", ip: "13.13.13.13"

	# Mount shared folder directly into webserver directory
	# Create two aliases "nfs" and "rsync" for different sync options.
	# Default is nfs as it won't throw an error if it is unsupported.
	# To use rsync, it must be installed an available in the path.
	config.vm.define "nfs", primary: true do |nfs|
		config.vm.synced_folder ".", "/var/www", type: "nfs"
	end

	config.vm.define "rsync", autostart: false do |rsync|

		# fix cygwin path on Windows hosts
		if Vagrant::Util::Platform.windows?
        	ENV["VAGRANT_DETECTED_OS"] = ENV["VAGRANT_DETECTED_OS"].to_s + " cygwin"
	  	end

		config.vm.synced_folder ".", "/var/www", type: "rsync",
			rsync__exclude: ".git/",
			rsync__args: ["--chmod=ugo=rwX","--verbose", "--archive", "--delete", "-z"]
	end

    # just copy files to a tmp folder, because copying will be done by the SSH user
    # who does not have root privileges.
    config.vm.provision "file", source: "vagrant_files/etc/apache2/sites-available/000-default.conf", destination: "/tmp/000-default.conf"

	# Provisioning the box with a shell script:
	config.vm.provision :shell, :path => "Vagrant_provision.sh"
end
