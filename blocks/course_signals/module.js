M.block_coursesignals = M.block_coursesignals || {};

M.block_coursesignals.init_signal = function(Y, message){
    
    YAHOO.namespace("signal.container"); 
    YAHOO.signal.container.signal_message = new YAHOO.widget.Panel("signal_message", { width:'300px', visible:false, constraintoviewport:true, draggable:true, close:true } );
    YAHOO.signal.container.signal_message.setHeader(M.str.block_course_signals.signal_message);
    YAHOO.signal.container.signal_message.setBody(message);
    YAHOO.signal.container.signal_message.render(document.body);
    YAHOO.util.Event.addListener("show_signal", "click", YAHOO.signal.container.signal_message.show, YAHOO.signal.container.signal_message, true); 

    var x = YAHOO.util.Dom.getX('container');
    var y = YAHOO.util.Dom.getY('container');

    YAHOO.util.Dom.setStyle('signal_message_c', 'left', x + 'px');
    YAHOO.util.Dom.setStyle('signal_message_c', 'top', y + 'px');
}