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
	config.vm.synced_folder ".", "/var/www", type: "nfs"

	# Provisioning the box with a shell script:
	config.vm.provision :shell, :path => "Vagrant_provision.sh"
end
