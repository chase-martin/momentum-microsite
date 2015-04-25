$(function(){
$(".editableCms").hover(
    function()
    {
        $(this).addClass("editHover");
    },
    function()
    {
        $(this).removeClass("editHover");
    }
);
    $(".editableCms").bind("click", replaceHTML);

$('.showCmsEditOverlay').click(function(){
    $('#cmsEditOverlay').toggle();
})

    $('.addVariant').click(function(){
        var value = $('#addVariant').val();
        var docId = $(this).attr('docId');
        $.ajax({
            type: "POST",
            url: '/api/cms/createVariant',
            data: ({variantName:value ,
                docId : docId}),
            success: function(){

            }
        });
    });

});

function replaceHTML()
{
    if(!event.altKey) return;
    event.stopPropagation();
    event.preventDefault();
    if ( $(this).find('.cmsEditBlock').length > 0)
       return;
    var oldText = $(this).html();
    var self = this;
    var data = {namespace : $(this).attr('cmsNameSpace'), docId: $(this).attr('cmsDocId') };
    $('.cmsEditBlock').remove();
    $(this).append('<div class="cmsEditBlock"> <textarea  >'+oldText+'</textarea>  <a  href="#" class="btnSave">Save </a><a href="#"  class="btnDiscard">Discard </a></div>');
    $(".btnSave").click(
        function(event)
        {
            event.preventDefault();
            event.stopPropagation();
            newText = $(".cmsEditBlock > textarea")
                .val();
            data.value = newText;
            $.ajax({
                type: "POST",
                url: '/api/cms/update',
                data: data,
                success: function(){},
            });

            $(self).html(newText);
        }
    );
    $(".btnDiscard").click(
        function(event)
        {
            event.preventDefault();
            event.stopPropagation();
            $(self).html(  oldText);
        }
    );
}
