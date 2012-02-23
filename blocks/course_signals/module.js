var block_width = YAHOO.util.Dom.getStyle('region-post', 'width');
block_width = block_width.split('px');
block_width = parseInt(block_width);

var signal_width = 300;
var dif = signal_width - block_width;

YAHOO.namespace("example.container"); 
YAHOO.example.container.signal_message = new YAHOO.widget.Panel("signal_message", { width:signal_width + 'px', visible:false, draggable:true, close:true } );
YAHOO.example.container.signal_message.setHeader("signal");
YAHOO.example.container.signal_message.render(document.body);
YAHOO.util.Event.addListener("show_signal", "click", YAHOO.example.container.signal_message.show, YAHOO.example.container.signal_message, true); 

var x = YAHOO.util.Dom.getX('container');
var y = YAHOO.util.Dom.getY('container');
var left = x - dif;
YAHOO.util.Dom.setStyle('signal_message_c', 'left', left + 'px');
YAHOO.util.Dom.setStyle('signal_message_c', 'top', y + 'px');