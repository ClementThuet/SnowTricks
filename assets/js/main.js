//Pour ajout/édit d'un média à un trick
//Masquer "cette img est-elle l'img principale ?" si la réponse est non à "Ce média est-il une img ?"
$isMainPicture=$("#formRowIsMainPicture");
$inputUrlImage=$("#inputUrlImage");
$inputUrlVideo=$("#inputUrlVideo");
$isImage=$('[name="media[isImage]"]:checked').val();
if($isImage === "0"){
    $isMainPicture.hide("fast");
    $inputUrlImage.hide("fast");
    $inputUrlVideo=$("#inputUrlVideo").show("fast");
}
else{
    $isMainPicture.show("fast");
    $inputUrlImage.show("fast");
    $inputUrlVideo=$("#inputUrlVideo").hide("fast");
}

$("[name='media[isImage]']").on('change', function() {
    $isImage=$('[name="media[isImage]"]:checked').val();
    if($isImage === "0"){
        $isMainPicture.hide("fast");
        $inputUrlImage.hide("fast");
        $inputUrlVideo=$("#inputUrlVideo").show("fast");
    }
    else{
        $isMainPicture.show("fast");
        $inputUrlImage.show("fast");
        $inputUrlVideo=$("#inputUrlVideo").hide("fast");
    }
});
//Show medias on click on button 
 $(document).ready(function(){
    // Load more data
    $("#show-medias").click(function(){
        $("#show-medias").css('display','none');
        $(".media-trick").css('display','block');
    });
});
//Charger plus de commentaire au click sur "voir plus"
$(document).ready(function(){
    $(".btn-comment-voir-plus").click(function(){
        var lastDisplayed = Number($("#row").val());
        var allcount = Number($("#all").val());
        var idFigure =$("#idFigure").val();
        var nbCommentsBase = 5;
        var nbMessageToAdd = 5;
        //Si ceux à charger sont la suite de ceux chargé de base
        //if(nbCommentsBase === lastDisplayed + nbCommentsBase){
        if(nbCommentsBase === lastDisplayed + nbCommentsBase){
            lastDisplayed+=nbCommentsBase; 
        }
        else{ //Si ceux à charger sont la suite de commentaires déja chargés
            lastDisplayed+=nbMessageToAdd;
        }
        if(lastDisplayed <= allcount){
            $("#row").val(lastDisplayed);
            $.ajax({
                url: "/afficher-plus-de-commentaires",
                type: "POST",
                data: {"idFigure":idFigure,"firstMessage":lastDisplayed,"nbMessageToAdd":nbMessageToAdd},
                async: true,
                beforeSend(){
                    $(".btn-comment-voir-plus").text("Chargement...");
                },
                success(response){
                    // appending posts after last comment
                    $(".commentaire:last").after(response).show().fadeIn("slow");

                    var rowno = lastDisplayed + nbCommentsBase;
                    // checking row value is greater than allcount or not
                    if(rowno >= allcount ){

                        // Change the text and background
                        $(".btn-comment-voir-plus").hide();
                    }else{
                        $(".btn-comment-voir-plus").text("Voir plus");
                    }
                }
            });
        }
        else{
            $(".btn-comment-voir-plus").hide();
        }
    });
});

//Charger plus de tricks au click sur "voir plus"
$(document).ready(function(){
    $(".btn-voir-plus-tricks").click(function(){
        
        var lastDisplayed = Number($("#row").val());
        var allcount = Number($("#all").val());
        var nbTricksBase = 8;
        var nbTricksToAdd = 8;
        //Si ceux à charger sont la suite de ceux chargé de base
        //if(nbTricksBase === lastDisplayed + nbTricksBase){
        if(nbTricksBase === 0){
            lastDisplayed+=nbTricksBase; 
        }
        else{ //Si ceux à charger sont la suite de commentaires déja chargés
            lastDisplayed+=nbTricksToAdd;
        }
        if(lastDisplayed <= allcount){
            $("#row").val(lastDisplayed);
            $.ajax({
                url: "/afficher-plus-de-figures",
                type: "POST",
                data: {"firstTrick":lastDisplayed,"nbTricksToAdd":nbTricksToAdd},
                async: true,
                beforeSend(){
                    $(".btn-voir-plus-tricks").text("Chargement...");
                },
                success(response){
                    // appending posts after last comment
                    $(".figure:last").after(response).show().fadeIn("slow");

                    var rowno = lastDisplayed + nbTricksBase;
                    // checking row value is greater than allcount or not
                    if(rowno >= allcount ){

                        // Change the text and background
                        $(".btn-voir-plus-tricks").hide();
                    }else{
                        $(".btn-voir-plus-tricks").text("Voir plus");
                    }
                }
            });
        }
        else{
            $(".btn-voir-plus-tricks").hide();
        }
    });
});


var $collectionHolder;
// setup an "add a tag" link
var $addMediaButton = $('<button type="button" class="add_media_link btn medias-btns">Ajouter un média</button>');
var $newLinkLi = $('<div class="media-wrapper"></div>').append($addMediaButton);

jQuery(document).ready(function() {
    // Get the ul that holds the collection of tags
    $collectionHolder = $('ul.medias');
    
    // add the "add a tag" anchor and li to the tags ul
    $collectionHolder.append($newLinkLi);
    
    // add a delete link to all of the existing tag form li elements
    $collectionHolder.find('li').each(function() {
        addMediaFormDeleteLink($(this));
    });
    // count the current form inputs we have (e.g. 2), use that as the new
    // index when inserting a new item (e.g. 2)
    $collectionHolder.data('index', $collectionHolder.find(':input').length);

    $addMediaButton.on('click', function(e) {
        // add a new tag form (see next code block)
        addMediaForm($collectionHolder, $newLinkLi);
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
    var $newFormLi = $('<div class="media-wrapper"></div>').append(newForm);
    addMediaFormDeleteLink($newFormLi);
    $newLinkLi.before($newFormLi);
}

function addMediaFormDeleteLink($mediaFormLi) {
    var $removeFormButton = $('<button type="button" class="btn medias-btns btns-delete-media">Supprimer ce média</button>');
    $mediaFormLi.append($removeFormButton);

    $removeFormButton.on('click', function(e) {
        // remove the li for the tag form
        $mediaFormLi.remove();
    });
}

$(document).ready(function(){
    $(".btn-delete-media-already-saved").click(function(){
    var $idToDelete =$(this).data("id");
    var $wrapper= $("[data-id="+$idToDelete+"]");
    ////var $wrapperToDelete  = (this).closest(".media-already-saved");
   // var $wrapperToDelete = $('.media-already-saved');
    
    $wrapper.fadeOut();
    $wrapper.remove();
    //var idToDelete = $wrapperToDelete.data("id");
    console.log($wrapper);
    });
});
