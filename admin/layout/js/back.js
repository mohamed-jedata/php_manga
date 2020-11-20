$(document).ready(function () {
    

  //confirm function
  $('.confirm').click(function () {

      return confirm("Are You Sure ?")

  });

  //show modifications button when over on cards

  $('.card').click(function () {
    
      $(this).toggleClass('ClickOnCard').find('.modify').toggle(); 

    }
  );

   //show ... after desc when it pass 42 caracters

   $('.card .desc').each(function () {

      if($(this).text().length >= 70){
        var newDesc = $(this).text().slice(0,70);
        $(this).text(newDesc+" ...");
      }
   });
   

   //make input empty when checkbox is checked

   $('.add-chapter #manga_id').change(function () { 

    var manga_id = $('.add-chapter #manga_id').val();;
   
    if(manga_id != ""){
      $('.add-chapter #title').attr("readonly",false);
      // get last chapter number with manga_id (ajax)

      $.ajax({url: "get_with_ajax.php?manga_id="+manga_id, success: function(result){
           $('.add-chapter #chapter_number').val(result);
           $('.add-chapter #title').val("Chapter "+result+" ");
      }});


    }
     
   });




});