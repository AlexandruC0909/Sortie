    console.log("Hello World");
    var $ville = $('#sortie_form_ville')
    var $token = $('#sortie_form__token')
    $ville.change(function (){
    var $form = $(this).closest('form')
    var data = {}
    data[$token.attr('name')] =$token.val()
    data[$ville.attr('name')] = $ville.val()

    console.log($ville.val())

    console.log($token.val())

    $.ajax({
    url : $form.attr('action'),
    type: $form.attr('method'),
    data : data,
    success: function(html) {
    console.log($ville.val())
    console.log(data)
    // Replace current position field ...
    $('#sortie_form_lieu').replaceWith(
    // ... with the returned one from the AJAX response.
    $(html).find('#sortie_form_lieu')
    );
    // Position field now displays the appropriate positions.
}
})
});

