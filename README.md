# Croogo: FilesAttachments Plugin

**FilesAttachments** is a free, open source, Attachment manager for nodes in the Croogo CMS for PHP, released under [MIT License].

It uses jQuery and ajax for the UI.

## Requirements
  * Croogo 1.5 or higher
  * HTML5 support
  * Javascript support
  * jQuery (included in Croogo 1.5)

## Installation

#### Web based installer

  * Upload the .zip file through Croogo's extension manager.

#### Manual installation

  * Extract the file. Upload the content to your Croogo installation in the ./app/Plugins/FilesAttachments directory.
  * visit Croogo's extension system to "activate" the plugin.

#### Create this folders

  * Create a folder called "uploads" in ./app/webroot, set the proper permissions to be able to upload files, this is done internally if apache has the rights to do it, but if you see an error, just create it manually.
  * Create a folder called "filesuploads" in ./app/tmp, set the proper permissions to be able to upload files, this is done internally if apache has the rights to do it, but if you see an error, just create it manually.

## How to use

  * Create a node of any type.
  * Add/Edit the content as usual
  * A new tab that says Attachments with (hopefully) a 0 (zero) will appear (this represents the number of attachments for the current node)
  * Click the tab
  * Save the node.

