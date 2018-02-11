var page = require('webpage').create();
page.settings.userAgent = "Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.5; en-US; rv:1.9.2.3) Gecko/20100401 Firefox/3.6.3";
page.viewportSize = {width: 1280, height: 1024};

var fs = require('fs');

page.open("https://www.diretta.it/calcio/italia/serie-b/risultati/", function(status){
   console.log("Status: " + status);


   if(status !== 'success'){
       console.log('Unable to load the address!');
       phantom.exit();
   }
   else{
       window.setTimeout(function () {
           var users = page.evaluate(function () {
              return document.querySelector("table.soccer").outerHTML;
           });
           page.render('example.png');
           var path = "output.html";
           var content = users; //page.content;
           fs.write(path, content, 'w');
           console.log("Load the address successfully");
           phantom.exit();
       }, 30000); // Change timeout as required to allow sufficient time
   }

});