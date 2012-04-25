(function($){
  Drupal.NodeAutosave = Drupal.NodeAutosave || {};

  Drupal.behaviors.NodeAutosave = {
    attach: function(context) {
      $('.nodeautosave-button').each(function(i){
        $(this).once('NodeAutosave-button', function(){
          $(this).bind('click', Drupal.NodeAutosave.NodeAutosaveSave);
        });
      });
      $("#" + Drupal.settings.NodeAutosave.formId).once('NodeAutosave', function(){
        window.setInterval(Drupal.NodeAutosave.NodeAutosaveSave, Drupal.settings.NodeAutosave.autosavePeriod * 1000);
      });
    }
  };

  Drupal.NodeAutosave.NodeAutosaveSave = function() {
    var saveButton = $('#edit-node-autosave-save-button-bottom');
    if (CKEDITOR) {
      $.each(CKEDITOR.instances, function(k,ck) { // foreach CKEDITOR instance
        ck.updateElement(); // update the textarea element so that it is autosaved
      });
    }
    saveButton.parents('form.node-form').ajaxSubmit({
      url: Drupal.settings.NodeAutosave.submitUrl,
      data: {_triggering_element_name: saveButton.attr('name'), _triggering_element_value: saveButton.attr('value')},
      dataType: 'json',
      success: NodeAutosaveResponse
    });
    return false;
  }

  function NodeAutosaveResponse(response) {
    if (typeof response == 'string') {
      response = $.parseJSON(response);
    }
    for(control in response) {
      $(response[control].selector).replaceWith(response[control].data);
    }
    $('.node-autosave-restore-button').removeClass('form-button-disabled').removeAttr('disabled');
    Drupal.attachBehaviors();
  }

})(jQuery)
