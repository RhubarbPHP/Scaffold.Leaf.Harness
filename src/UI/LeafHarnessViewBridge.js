window.rhubarb.vb.create("LeafHarnessViewBridge", function(){
   return {
       onReady: function(){
           var leafSelect = this.findChildViewBridge("leafClass");

           if (leafSelect){
               leafSelect.attachClientEventHandler("ValueChanged", function(bridge, value){
                  window.location.href="/harness/" + value;
               });
           }
       }
   }
});