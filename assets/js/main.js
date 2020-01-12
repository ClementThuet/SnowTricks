
//Demandede confirmation pour la suppression d'une figure
$(".delete-trick").click(function() {
    if (window.confirm("Êtes-vous certain de vouloir supprimer cette figure ?")) { 
        $url=$(".delete-trick").data("url");
        window.location.replace($url);
    }
});
$(".delete-media").click(function() {
    if (window.confirm("Êtes-vous certain de vouloir supprimer ce média ?")) { 
        $url=$(".delete-media").data("url");
        window.location.replace($url);
    }
});
//Pour ajout/édit d'un média à un trick
//Masquer "cette img est-elle l'img principale ?" si la réponse est non à "Ce média est-il une img ?"
$isMainPicture=$("#formRowIsMainPicture");
$inputUrlImage=$("#inputUrlImage");
$inputUrlVideo=$("#inputUrlVideo");
$isImage=$('[name="media[isImage]"]:checked').val();
if($isImage === '0'){
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

//Charger plus de commentaire au click sur "voir plus"
$(document).ready(function(){
    // Load more data
    $(".btn-comment-voir-plus").click(function(){
        var lastDisplayed = Number($("#row").val());
        var allcount = Number($("#all").val());
        var idFigure =$("#idFigure").val();
        var nbCommentsBase = 5;
        var nbMessageToAdd = 5;
        //Si ceux à charger sont la suite de ceux chargé de base
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
            // Setting little delay while removing contents
           /* setTimeout(function() {

                // When row is greater than allcount then remove all class='post' element after 3 element
                $('.commentaire:nth-child(2)').nextAll('.commentaire').remove();

                // Reset the value of row
                $("#row").val(0);

                // Change the text and background
                $('.voir-plus').text("Voir moins");
                
            }, 2000);*/
        }
    });
});
