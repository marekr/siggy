<!DOCTYPE html> 
<link rel="stylesheet" type="text/css" media="screen,projection" href="http://localhost/evetel/css/dark-hive/jquery-ui-1.8.5.custom.css">

<style type="text/css">
* {

padding:0;
margin:0;
}
html, body
{
height: 100%;
background: #000;
color: #FFF;
font-size:12px;
} 

.cursor {
    cursor: -moz-grab;
}

.drag_cursor {
    cursor: move;
    cursor: -moz-grabbing;
}


</style>
<script type="text/javascript" src="jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="jquery-ui-1.8.5.custom.min.js"></script>
<script type="text/javascript" src="jquery.mousewheel.min.js"></script>
<div>
<div style="padding:5px;" class="ui-widget-header">
  <div class="ui-widget" style="float:left;">
    <label for="system">System: </label>
    <input id="system" />
  </div>

  <div class="ui-widget" style="margin-left:20px;margin-top:5px;float:left;">
    <span>Zoom:</span> <div id="slider" style="width:300px;float:right"></div>
  </div>
  <div style="clear:both;"></div>
</div>
<div style="margin-right:400px;height:100%;position:relative;">
<canvas id="map" style="background:#FFF;height:100%;width:100%;overflow:hidden;position:relative;border: 1px solid #000;float:left;padding:0;margin:0;"></canvas>
<div style="width:400px;position:absolute;right:-400px;height:100%;background: #262526;">
  <div id="tabs">
      <ul>
        <li><a href="#tabs-1">Route Planner</a></li>
        <li><a href="#tabs-1">Jump Planner</a></li>
        <li><a href="#tabs-1">Details</a></li>
      </ul>
  <div style="clear:both;"></div>
      <div id="tabs-1">

        <fieldset>
          <legend>Basic Routing</legend>
          <div class="ui-widget">
            <label for="sourcesys">Source: </label>
            <input id="sourcesys" />
          </div>


          <div class="ui-widget">
            <label for="destsys">Destination: </label>
            <input id="destsys" />
          </div>
          <br />
          <button>Clear</button>
          <button>Find Route</button>
          <button>Reverse Route</button>

        </fieldset>
  <div style="clear:both;"></div>

      </div>
  </div>
  <div style="clear:both;"></div>
</div>

</div>
  <div style="clear:both;"></div>
</div>


<script type="text/javascript">
$(function() {
  //array mappings for systems and jumps
  var jumpsh = { 'fromX':0, 'fromY':1,'toX':2,'toY':3,'regional':4 };
  var sysh = { 'x':0,'y':1,'name': 2,'sec': 3 };
  
  var container = $('#mapWrap');
  var map = $('#map');
  var canvas = map[0];
  var ctx = canvas.getContext("2d");
  canvas.height = map.height();
  canvas.width = map.width();
  var baseWidth = canvas.width;
  var baseHeight = canvas.height;
  var absHeight = 2048;
  var absWidth = 1856;
  
  var zoom = 1;
  var dragged = false;
  var dx = 0;
  var dy = 0;
  var mx = 0;
  var my = 0;
  var drawing = false;
  
  var width = canvas.width;
  var height = canvas.height;
  
  var x1 = 0;
  var y1 = 0;
  var x2 = canvas.width;
  var y2 = canvas.height;
  
  ctx.font = "5pt sans-serif";
  ctx.lineWidth   = 2;
  ctx.strokeStyle = '#2B47C4';

  $("#tabs").tabs();
	$(function() {
		$( "button, input:submit" ).button();
	});
	
		$("#slider").slider({
			value:20,
			min: 20,
			max: 100,
			step: 20,
			slide: function(event, ui) {
				//$("#amount").val('$' + ui.value);
			}
		});
	
  

  var data = [
  <?php foreach($systems as $system): ?>
    { label: "<?php echo $system['name']?>", value: "<?php echo $system['id']?>" },
  <?php endforeach; ?>		
  ];
		
  $( "#system" ).autocomplete({
    source: data,
    minLength: 2,			
    focus: function( event, ui ) {
				$( "#system" ).val( ui.item.label );
				return false;
			},

    select: function( event, ui ) {
      $('#system').val( ui.item.label );
      systemFocus( ui.item.value );
      return false;
    }


  });
 
  
  $( window ).resize( function() {
    width = container.width();
    height = container.height();
  } );
  

  $('#map').mousedown(function(e){ return drag_start(e); }).
    mousemove(function(e){return drag(e)}).
    mouseup(function(e){return drag_end(e)}).
    mouseleave(function(e){return drag_end(e)}).
        mousewheel(function(ev, delta)
        {
            //this event is there instead of containing div, because
            //at opera it triggers many times on div
            var change = (delta > 0)?1:-1;
            do_zoom(change);
            return false;
        });
  
  function systemFocus( id )
  {
    setCoords(systems[ id ][sysh.x]*zoom, systems[ id ][sysh.y]*zoom);
  }      
        
  function do_zoom(delta)
  {
    //don't take input while drawing
    if ( drawing )
    {
      return;
    }
    
    if( delta == 1 )
    {
      if( zoom == 10 )
      {
        zoom = 10;
        return;
      }
      zoom += 1;

    }
    else if( delta == -1 )
    {
      if( zoom == 1 )
      {
        zoom = 1;
        return;
      }
      zoom -= 1;
    }
      x1 *= (zoom/(zoom-delta));
      x2 *= (zoom/(zoom-delta));
      y1 *= (zoom/(zoom-delta));
      y2 *= (zoom/(zoom-delta));

   // canvas.width = baseWidth*zoom;
  //  canvas.height = baseHeight*zoom;
    
    var old_x = -x1 + Math.round(width/2);
    var old_y = -y1 + Math.round(height/2);
    var new_x = (old_x/(zoom-delta))*zoom;
    var new_y = (old_y/(zoom-delta))*zoom;
    
    //new_x = width/2 - new_x;
    //new_y = height/2 - new_y;

   // console.log(new_x);
    
    //setCoords(new_x, new_y);
    draw();
  }      
        
      

  /**
  *   callback for handling mousdown event to start dragging image
  **/
  function drag_start(e)
  {
     // if(this.settings.onStartDrag && 
     //    this.settings.onStartDrag.call(this,this.getMouseCoords(e)) == false)
     // {
     //     return false;
     // }
      
      /* start drag event*/
      dragged = true;
      map.addClass("drag_cursor");

      dx = e.pageX - mx;
      dy = e.pageY - my;
      return false;
  }
  
  /**
  *   callback for handling mousmove event to drag image
  **/
  function drag(e)
  {
      
      if(dragged){
                  
          var ltop =  Math.round((dy - e.pageY));
          var lleft = Math.round((dx - e.pageX));
          
          x2 += lleft;
          if( x2 < width )
          {
            x2 = width;
            x1 = 0;
          }
          else if( x2 > absWidth*zoom )
          {
            x2 = absWidth*zoom;
            x1 = absWidth*zoom-width;
          }
          else
          {
            x1 += lleft;
          }
          
          y2 += ltop;
          if( y2 < height )
          {
            y2 = height;
            y1 = 0;
          }
          else if( y2 > absHeight*zoom )
          {
            y2 = absHeight*zoom;
            y1 = absHeight*zoom-height;
          }
          else
          {
            y1 += ltop;
          }          
          
          setCoords(lleft, ltop);
          draw();
         // console.log("x1:"+x1+"x2:"+x2+"y1"+y1+"y2:"+y2);
          return false;
      }
  }
  
  /**
  *   callback for handling stop drag
  **/
  function drag_end(e)
  {
      map.removeClass("drag_cursor");
      dragged=false;
  }
  

        
  /**
  * set coordinates of upper left corner of image object
  **/
  function setCoords(x,y)
  {
      //check new coordinates to be correct (to be in rect)
      if(y > 0){
          y = 0;
      }
      if(x > 0){
          x = 0;
      }
      if(y + canvas.height < height){
          y = height - canvas.height;
      }
      if(x + canvas.width < width){
          x = width - canvas.width;
      }
      if(canvas.width <= width){
          x = -(canvas.width - width)/2;
      }
      if(canvas.height <= height){
          y = -(canvas.height - height)/2;
      }
      
      mx = x;
      my = y;
     //map.css("top",Math.round(y) + "px")
     //                  .css("left",Math.round(x) + "px");
  }    
  
  function drawSystems()
  {
    var tx;
    var ty;
    
    ctx.lineWidth   = 2;
    for(i in systems)
    {
      tx = systems[i][sysh.x]*zoom;
      ty = systems[i][sysh.y]*zoom;
      
      if( (tx >= x1 ) && (tx <= x2 ) && (ty >= y1 ) && (ty <= y2 ) )
      {
        ctx.beginPath();
        if( zoom > 2 )
        {
        ctx.arc(systems[i][sysh.x]*zoom-x1, systems[i][sysh.y]*zoom-y1, 3, 0, Math.PI*2, true); 
        ctx.closePath();
        ctx.stroke();
        }
        else
        {
        ctx.arc(systems[i][sysh.x]*zoom-x1, systems[i][sysh.y]*zoom-y1, 1, 0, Math.PI*2, true); 
        ctx.closePath();
        ctx.fill();
        }
        if( zoom > 2 )
        {
          ctx.fillStyle = "#FFF";
          ctx.fillText( systems[i][sysh.name]-x1, systems[i][sysh.x]*zoom + 5, systems[i][sysh.y]*zoom - 10);
        }
      }
    }
  }
  
  function drawJumps()
  {
    ctx.lineWidth   = 1;
    for(i in jumps)
    {
      ctx.beginPath();
      ctx.moveTo(jumps[i][jumpsh.fromX]*zoom,jumps[i][jumpsh.fromY]*zoom);  
      ctx.lineTo(jumps[i][jumpsh.toX]*zoom,jumps[i][jumpsh.toY]*zoom);  
      
      if(jumps[i][jumpsh.regional] == 1)
      {
        ctx.strokeStyle = '#C42B47';
      }
      ctx.stroke();  
      if(jumps[i][jumpsh.regional] == 1)
      {
        ctx.strokeStyle = '#2B47C4';
      }
    }
  }
  
  function draw()
  {
    if( drawing )
    {
      return;
    }
    drawing = true;
    
    ctx.clearRect(0,0,canvas.width,canvas.height);       
    drawSystems();
   // drawJumps();
    
    drawing = false;
  }
  
  var systems = new Array();
  <?php foreach($systems as $system): ?>
    systems[<?php echo $system['id']?>] = ([<?php echo $system['x']?>,<?php echo $system['y']?>,"<?php echo $system['name']?>"]);
  <?php endforeach; ?>
  
  var jumps = new Array();
  <?php foreach($jumps as $jump): ?>
    jumps.push([<?php echo $jump['fromX']; ?>,<?php echo $jump['fromY']; ?>,<?php echo $jump['toX']; ?>,<?php echo $jump['toY']; ?>,<?php echo ($jump['regional']?1:0); ?>]);
  <?php endforeach; ?>
  
  draw();
  
 
	//$("#map").panView(1024,800);
	//$("#mapWrap").evemap( function() {
	//} );
	$("#zoomplus").click( function() {
    zoom += 1;
    canvas.width = baseWidth*zoom;
    canvas.height = baseHeight*zoom;
    draw();
	} );
	$("#zoomminus").click( function() {
    zoom -= 1;
    canvas.width = baseWidth*zoom;
    canvas.height = baseHeight*zoom;
    draw();
	} );

});
</script>