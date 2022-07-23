import { Application } from "@hotwired/stimulus"
import FormCollectionController from "@braunstetter/media-bundle/controllers/collection_controller"
import ImageUploadController from "@braunstetter/media-bundle/controllers/image_upload_controller"

window.Stimulus = Application.start()
Stimulus.debug = true;
Stimulus.warnings = true;
Stimulus.register('braunstetter--media-bundle--collection', FormCollectionController)
Stimulus.register('braunstetter--media-bundle--image-upload', ImageUploadController)