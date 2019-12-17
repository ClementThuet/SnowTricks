//Masquer "cette img est-elle l'img principale ?" si la réponse est non à "Ce média est-il une img ?"

$isMainPicture=$('#formRowIsMainPicture');
$inputUrlImage=$('#inputUrlImage');
$inputUrlVideo=$('#inputUrlVideo');
$isImage=$('[name="media[isImage]"]:checked').val();
if($isImage === '0'){
    $isMainPicture.hide("fast");
    $inputUrlImage.hide("fast");
    $inputUrlVideo=$('#inputUrlVideo').show("fast");
}
else{
    $isMainPicture.show("fast");
    $inputUrlImage.show("fast");
    $inputUrlVideo=$('#inputUrlVideo').hide("fast");
}

$("[name='media[isImage]']").on('change', function() {
    $isImage=$('[name="media[isImage]"]:checked').val();
    if($isImage === '0'){
        $isMainPicture.hide("fast");
        $inputUrlImage.hide("fast");
        $inputUrlVideo=$('#inputUrlVideo').show("fast");
    }
    else{
        $isMainPicture.show("fast");
        $inputUrlImage.show("fast");
        $inputUrlVideo=$('#inputUrlVideo').hide("fast");
    }
});
/* setup an "add a tag" link
var $addMediaButton=$('.add_media_link');
//var $newLinkLi = $('<li></li>').append($addMediaButton);
var $newLinkLi = $('<li></li>');

jQuery(document).ready(function() {
    
    var $collectionHolder;
    // Get the ul that holds the collection of tags
    $collectionHolder = $('ul.medias');

    // add the "add a tag" anchor and li to the tags ul
     $collectionHolder.append($newLinkLi);

    // count the current form inputs we have (e.g. 2), use that as the new
    // index when inserting a new item (e.g. 2)
    $collectionHolder.data('index', $collectionHolder.find(':input').length);
    
    $addMediaButton.on('click', function(e) {
        // add a new tag form (see next code block)
        addMediaForm($collectionHolder, $newLinkLi);
    });
    // Get the ul that holds the collection of tags
    $collectionHolder = $('ul.medias');
    
    
    
    //To get the "autre choix" value on groupe figure
    /*$('#update_figure_groupe').on('change',function(){
       var optionValue = $(this).val();
        var optionText = $('#update_figure_groupe option[value="'+optionValue+'"]').text();
        if (optionText ==='Autre groupe'){
            console.log('ok');
            $( ".dropdownGroupe" ).after( '<input type="text" id="update_figure_groupe" name="update_figure[groupe]" required="required" class="form-control" placeholder="Veuillez saisir le groupe de la figure">' );
        }
        //'<input type="text" id="update_figure_groupe" name="update_figure[groupe]" required="required" class="form-control" placeholder="Veuillez saisir le groupe de la figure" value="Rotation">'
    });
    
    
});

function addMediaForm($collectionHolder, $newLinkLi) {
    // Get the data-prototype explained earlier
    var prototype = $collectionHolder.data('prototype');
    // get the new index
    var index = $collectionHolder.data('index');

    var newForm = prototype;
    // You need this only if you didn't set 'label' => false in your tags field in TaskType
    // Replace '__name__label__' in the prototype's HTML to
    // instead be a number based on how many items we have
    // newForm = newForm.replace(/__name__label__/g, index);

    // Replace '__name__' in the prototype's HTML to
    // instead be a number based on how many items we have
    newForm = newForm.replace(/__name__/g, index);

    // increase the index with one for the next item
    $collectionHolder.data('index', index + 1);

    // Display the form in the page in an li, before the "Add a tag" link li
    var $newFormLi = $('<li></li>').append(newForm);
    $newLinkLi.before($newFormLi);
    
     // add a delete link to the new form
    addMediaFormDeleteLink($newFormLi);
}

function addMediaFormDeleteLink($mediaFormLi) {
    var $removeFormButton = $('<button type="button" class="btn btn-primary media-btn" >Supprimer ce media</button>');
    $mediaFormLi.append($removeFormButton);

    $removeFormButton.on('click', function(e) {
        // remove the li for the tag form
        $mediaFormLi.remove();
    });
}*/