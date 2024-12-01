function sendform(a,b,c) {
  		 var a="#"+a;
		 var c="#"+c;
		   $(a).submit(function(e){
			   e.preventDefault(); 
			   var setdata= $(a).serialize();
		       $.post( b, setdata, function( data ) { 
			   $(a).hide();
	           $(c).html( data ); 
	           })
              return false; 
			 }
			);
          }




