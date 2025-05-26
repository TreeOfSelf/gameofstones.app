<?php

/* establish a connection with the database */
include_once("admin/connect.php");
include_once("admin/userdata.php");
include_once("admin/locFuncs.php");

$wikilink = "Controlling+Cities";

$time=time();
$sort= $_REQUEST['sort'];

if ($message == '') $message = "";

//HEADER
include('header.php');
?>


<canvas 
style = "
    position: absolute;
    top: 53%;
	left:50%;
    transform: translate(-50%, -50%);     
	touch-action: none;
"
 id="map"></canvas>
 
 


	  
	  
	  
	  <div style = "

		position: absolute;
		top: 50%;
		left:87%;
		width:20%;
		height:70%;
		transform: translate(-50%, -50%);
		overflow-wrap: break-word;
		overflow-y: auto;
		font-family: 'Times New Roman', Times, serif;
		font-size:120%;
		text-shadow: 2px 2px #000000;
		max-width:100%;
		padding-left:0;
		padding-top:0px;
		
		"
	  
	    class="panel panel-travel" id="infoPanel">
        <div class="panel-heading">
          <h3 class="panel-title">Area Information</h3>
        </div>
          <p style="width:90%" id="info"></p>
      </div>


<script>


	wild = {
		"Aiel Waste" : ['Darkfriend' , 'Mad Male Channeler', 'Lion', 'Capar', 'Gara', 'Aiel'],
		"Almoth Plain" : ['Dragonsworn' , 'Whitecloak', 'Torm', 'Seancan', 'Lopar', "S'redit"],
		"Aryth Ocean": ['Bandit' , 'Torm', 'Grolm', 'Corlm', 'Damane', "Raken"],
		"Bay of Remara": ['Guard Name' , 'Wilder', 'Merchant Guard', 'Soldier', 'Brigand', "Drunken Soldier"],
		"Black Hills":['Guard Dog' , 'Trolloc', 'Dragonsworn', 'Wilder', 'Warder', "Aes Sedai"],
		"Blasted Lands": ['Darkhound' , 'Darkfriend', 'Mad Male Channeler', 'Myrddraal', 'Draghkar', "Jumara"],
		"Braem Wood": ['Solider','Guard Dog', 'Mad Male Channeler', 'Bandit', 'Mercenary', "Asha'man"],
		"Caralain Grass": ["Bandit",'Lion', 'Solider', 'Wolf', 'Black Ajah', 'Mercenary'],
		"Field of Merrilor": ['Trolloc','Dragonsworn','Myrddraal',"Asha'man","Grayman","Dreadlord"],
		"Forest of Shadows": ["Wolf","Bandit","Corlm","Torm", "Bear","Gholam"],
		"Garen's Wall": ["Torm","Wilder","Wolf","Whitecloak","Darkhound","Deathwatch Guard"],
		"Haddon Mirk": ['Soldier',"Bandit","Wilder","Darkfriend","Drunken Solider","Blacklance"],
		"Hills of Kintara": ["Grolm", "Soldier","Guard Dog","Merchant Guard","Darkfriend","Black Ajah"],
		"Kinslayer's Dagger": ["Lion","Trolloc","Bandit","Grolm","Aiel","Capar"],
		"Mountains of Mist": ["Merchant Guard","Wolf","Whitecloak","Trolloc","Lion","Wolfbrother"],
		"Paerish Swar": ["Wilder","Grolm","Wolf","Dragonsworn","Corlm","Bear"],
		"Plain of Lances": ["Myddraal","Solider","Trolloc","Darkhound","Aes Sedai","Draghkar"],
		"Plains of Maredo": ["Damane","Darkhound","Dragonsworn","Guard Dog","Whitecloak","Seanchan"],
		"Sea of Storms": ["Corlm","Merchant Guard","Damane","Raken","Asha'man","Brigand"],
		"Shadow Coast":  ["Whitecloak","Grolm","Torm","Damane","To'raken","Lopar"],
		"Spine of the World": ["Wilder","Guard Dog","Darkfriend","Lion","Capar","Gara"],
		"Tarwin's Gap": ["Trolloc","Myddraal","Darkhound","Mad Male Channeler","Gholam","Warder"],
		"Windbiter's Finger": ["Grolm","Damane","Merchant Guard","Brigand","Raken","To'raken"],
		"World's End": ["Mad Male Channeler","Corlm","Myddraal","Draghkar","Seanchan","Grayman"]
	}
	
	routes = {
		"Aiel Waste":["Rhuidean","Mayene","Thakan'dar","Stedding Shangtai"],"Almoth Plain":["Falme","Amador","Bandar Eban","Emond's Field"],"Amador":["Forest of Shadows","Almoth Plain","Shadow Coast","Black Hills"],"Aryth Ocean":["Tanchico","Falme","Cantorin","Bandar Eban"],"Bandar Eban":["Paerish Swar","World's End","Almoth Plain","Aryth Ocean"],"Bay of Remara":["Mayene","Tear","Stedding Shangtai","Cantorin"],"Black Hills":["Tar Valon","Chachin","Salidar","Amador"],"Blasted Lands":["Thakan'dar","Fal Dara","Cairhien","Caemlyn"],"Braem Wood":["Caemlyn","Cairhien","Chachin","Rhuidean"],"Caemlyn":["Braem Wood","Caralain Grass","Hills of Kintara","Blasted Lands"],"Cairhien":["Kinslayer's Dagger","Braem Wood","Blasted Lands","Haddon Mirk"],"Cantorin":["Windbiter's Finger","Shadow Coast","Aryth Ocean","Bay of Remara"],"Caralain Grass":["Salidar","Caemlyn","Jehannah","Ebou Dar"],"Chachin":["Plain of Lances","Black Hills","Braem Wood","World's End"],"Ebou Dar":["Shadow Coast","Windbiter's Finger","Sea of Storms","Caralain Grass"],"Emond's Field":["Mountains of Mist","Paerish Swar","Forest of Shadows","Almoth Plain"],"Fal Dara":["Tarwin's Gap","Blasted Lands","Spine of the World","Field of Merrilor"],"Falme":["Almoth Plain","Aryth Ocean","Garen's Wall","Sea of Storms"],"Far Madding":["Haddon Mirk","Hills of Kintara","Field of Merrilor","Plains of Maredo"],"Field of Merrilor":["Shol Arbela","Tar Valon","Far Madding","Fal Dara"],"Forest of Shadows":["Amador","Salidar","Emond's Field","Jehannah"],"Garen's Wall":["Jehannah","Tanchico","Falme","Mayene"],"Haddon Mirk":["Far Madding","Stedding Shangtai","Tear","Cairhien"],"Hills of Kintara":["Lugard","Far Madding","Caemlyn","Salidar"],"Illian":["Plains of Maredo","Sea of Storms","Kinslayer's Dagger","Windbiter's Finger"],"Jehannah":["Garen's Wall","Mountains of Mist","Caralain Grass","Forest of Shadows"],"Kinslayer's Dagger":["Cairhien","Shol Arbela","Illian","Thakan'dar"],"Lugard":["Hills of Kintara","Plains of Maredo","Mountains of Mist","Plain of Lances"],"Maradon":["World's End","Plain of Lances","Paerish Swar","Mountains of Mist"],"Mayene":["Bay of Remara","Aiel Waste","Plains of Maredo","Garen's Wall"],"Mountains of Mist":["Emond's Field","Jehannah","Lugard","Maradon"],"Paerish Swar":["Bandar Eban","Emond's Field","Maradon","Tanchico"],"Plain of Lances":["Chachin","Maradon","Shol Arbela","Lugard"],"Plains of Maredo":["Illian","Lugard","Mayene","Far Madding"],"Rhuidean":["Aiel Waste","Spine of the World","Tarwin's Gap","Braem Wood"],"Salidar":["Caralain Grass","Forest of Shadows","Black Hills","Hills of Kintara"],"Sea of Storms":["Tear","Illian","Ebou Dar","Falme"],"Shadow Coast":["Ebou Dar","Cantorin","Amador","Tear"],"Shol Arbela":["Field of Merrilor","Kinslayer's Dagger","Plain of Lances","Spine of the World"],"Spine of the World":["Stedding Shangtai","Rhuidean","Fal Dara","Shol Arbela"],"Stedding Shangtai":["Spine of the World","Haddon Mirk","Bay of Remara","Aiel Waste"],"Tanchico":["Aryth Ocean","Garen's Wall","Windbiter's Finger","Paerish Swar"],"Tar Valon":["Black Hills","Field of Merrilor","World's End","Tarwin's Gap"],"Tarwin's Gap":["Fal Dara","Thakan'dar","Rhuidean","Tar Valon"],"Tear":["Sea of Storms","Bay of Remara","Haddon Mirk","Shadow Coast"],"Thakan'dar":["Blasted Lands","Tarwin's Gap","Aiel Waste","Kinslayer's Dagger"],"Windbiter's Finger":["Cantorin","Ebou Dar","Tanchico","Illian"],"World's End":["Maradon","Bandar Eban","Tar Valon","Chachin"]
	}

	ctx = map.getContext("2d");
	map.width = window.innerWidth*1.0;
	while(map.width>1000 || map.width*0.75 >window.innerHeight*0.85){
		map.width-=100;
		
	}
	
	map.height = map.width*0.75
	mapRatio = window.innerWidth/1280
	map.style.width=map.width+'px'	


	
	let mapImg = new Image();
	let rockImg = new Image();
	let caveImg = new Image();
	let cityImg = new Image();
	let wildImg = new Image();
	let bushImg = new Image();
	let treeImg = new Image();
	let mountainImg = new Image();
	let sandImg = new Image();
	
	
	
	sandImg.src = "images/map/114.png";
	cityImg.src = "images/map/4.png";
	rockImg.src = "images/map/134.png";
	bushImg.src = "images/map/84.png";
	mountainImg.src = "images/map/31.png";
	treeImg.src = "images/map/88.png";
	caveImg.src = "images/map/68.png";
	mapImg.src = "images/map.bmp";


	dragging = false;
	xOff=0;
	yOff=0;
	scale=1.0;
	
	window.onresize=function(){
			
		map.width = window.innerWidth*0.9;
		while(map.width>1000 || map.width*0.75 >window.innerHeight*0.85){
			map.width-=100;
			
		}
		map.height = map.width*0.75
		mapRatio = window.innerWidth/1280
		map.style.width=map.width+'px'	
	
	
	}
	
	function distance(p1,p2){
		var dx = p2[0]-p1[0];
		var dy = p2[1]-p1[1];
		return Math.sqrt(dx*dx + dy*dy);
	}
	selected = [];
	elements=[];
	get=0;
	flags=[];
	sigils=[];
	
	function draw_area(type,x,y,name,offSetX=0,offSetY=0){
		

		ctx.font = '15px serif';
		ctx.textBaseline = 'hanging';

		ctx.textAlign = "center";
		let scaleMod=1;
		
		if(get==0){
			if(selected.includes(name)){
				scaleMod=1.4;
			}
			if(selected[selected.length-1]==name){
				ctx.beginPath();
				ctx.strokeStyle="red";
				ctx.arc(((x+28)*scale-scaleMod*1.2)+xOff,((y+28)*scale-scaleMod*1.2)+yOff,20, 0, 2 * Math.PI);
				ctx.arc(((x+28)*scale-scaleMod*1.2)+xOff,((y+28)*scale-scaleMod*1.2)+yOff,30, 0, 2 * Math.PI);
				ctx.arc(((x+28)*scale-scaleMod*1.2)+xOff,((y+28)*scale-scaleMod*1.2)+yOff,50, 0, 2 * Math.PI);
				ctx.stroke();
			}
			
			var charLoc = document.getElementById("char");
			if(charLoc!=null){
			if(charLoc.innerHTML.replace('"',"").replace('"',"")==name){
				ctx.beginPath();
				ctx.strokeStyle="blue";
				ctx.arc(((x+20*scaleMod)*scale-scaleMod*1.2)+xOff,((y+20*scaleMod)*scale-scaleMod*1.2)+yOff,20, 0, 2 * Math.PI);
				ctx.arc(((x+20*scaleMod)*scale-scaleMod*1.2)+xOff,((y+20*scaleMod)*scale-scaleMod*1.2)+yOff,30, 0, 2 * Math.PI);
				ctx.arc(((x+20*scaleMod)*scale-scaleMod*1.2)+xOff,((y+20*scaleMod)*scale-scaleMod*1.2)+yOff,40, 0, 2 * Math.PI);
				ctx.stroke();
			}
			}
			
			if(flags[name]==null){
				var cityData =  document.getElementById(name);
				if(cityData!=null){
					cityData = cityData.innerHTML.split('@');
					var flag = JSON.parse(cityData[1]);
					var sigil = JSON.parse(cityData[2]);
					console.log(sigil);
					if(flag!=null){
						var flagImage = new Image();
						flagImage.src = "images/Flags/"+flag;
						flags[name]=flagImage;
						var sigilImage = new Image();
						sigilImage.src = "images/Sigils/"+sigil;
						sigils[name]=sigilImage;
					}
				}
				//var flag = JSON.parse(cityData[1]);
				
			}
			if(flags[name]!=null){
				type=flags[name];
			}

			ctx.drawImage(type,(x*scale-scaleMod*1.2)+xOff,(y*scale-scaleMod*1.2)+yOff,19*scale*2.0*scaleMod,19*scale*2.0*scaleMod);
			
			if(sigils[name]!=null){
				ctx.drawImage(sigils[name],(x*scale-scaleMod*1.2)+xOff,(y*scale-scaleMod*1.2)+yOff,19*scale*2.0*scaleMod,19*scale*2.0*scaleMod);
			}
			
			elements.push([(x*scale)+xOff+9,(y*scale)+yOff+9,name]);
		}else{
	
			if(selected.includes(name)){
				x+=offSetX;
				y+=offSetY;
				ctx.fillStyle = "rgba(0, 0, 0, 0.5)";
				ctx.fillRect((x*scale)+xOff-2-50,(y*scale)+yOff-25,
				120,20
				)

				ctx.fillStyle="black";
				ctx.fillText(name,(x*scale)+xOff-2+60-50,(y*scale)+yOff-20);
				ctx.fillStyle="white";
				ctx.fillText(name,(x*scale)+xOff+60-50,(y*scale)+yOff-20);
			}
		}
	}
	
	map.onmousedown=function(){
		dragging=true;
	}
	
	
	var touchX=0;
	var touchY=0;
	
	map.ontouchstart=function(e){
		dragging=true;
		touchX = e.touches[0].clientX;
		touchY = e.touches[0].clientY;
	}
	
	map.onmouseup=function(){
		dragging=false;
	}
	
	map.ontouchend=function(){
		dragging=false;
	}
	map.onmousemove=function(e){
		if(dragging==true){
			xOff+=e.movementX;
			yOff+=e.movementY;
		

		}
	}

	map.ontouchmove=function(e){
		if(dragging==true){
			xOff+=(e.touches[0].clientX-touchX)*0.13;
			yOff+=(e.touches[0].clientY-touchY)*0.13;
		

		}
	}

	map.onclick=function(e){
		for(var k =0; k<elements.length;k++){

			//console.log([e.clientX-map.offsetLeft+map.width/2,e.pageY-map.offsetTop+map.height/2],elements[k]);

			console.log(e.pageY);
			if(distance([e.clientX-map.offsetLeft+map.width/2,e.pageY-map.offsetTop+map.height/2],elements[k])<25){
							console.log(elements[k]);
				selected = [...routes[elements[k][2]]]
				selected.push(elements[k][2]);
				console.log(selected);
				if(wild[elements[k][2]]!=null){
					info.innerHTML="";
					info.innerHTML+="<div style='font-size:25px'>"+elements[k][2]+"</div>";
					info.innerHTML+="<a style='font-size:23px' href='ways.php?area="+elements[k][2]+"'>Travel</a></br></br>"
					info.innerHTML+="<u><b>Enemies in this area</b></u>: </br>";
					for(var l=0;l<wild[elements[k][2]].length;l++){
						info.innerHTML+=wild[elements[k][2]][l]+'</br>';
					}
				}else{
					var cityData =  document.getElementById(elements[k][2]).innerHTML.split('@');
					var flag = JSON.parse(cityData[1]);
					var sigil = JSON.parse(cityData[2]);
					cityData = JSON.parse(cityData[0]);
					info.innerHTML=""
					info.innerHTML+="<div style='font-size:25px'>"+elements[k][2]+"</div>";
					info.innerHTML+="<a style='font-size:23px' href='ways.php?area="+elements[k][2]+"'>Travel</a></br></br>"
					info.innerHTML+="<u><b>Ruler</b></u>: </br>"+cityData.ruler+'</br>';
					  
					if(flag!="" && flag!=null){
						
						var flagImage = document.createElement("div");
						flagImage.style.backgroundImage = 'url("images/Flags/'+flag+'")';
						flagImage.style.backgroundRepeat = "no-repeat";
						flagImage.style.backgroundPosition="center";
						//flagImage.style.width=map.width*0.1+'px';
						//flagImage.style.height=(map.width*0.1)*3+"px";
						flagImage.width=193
						flagImage.height=197;
						
						info.appendChild(flagImage);
						

						
						var sigilImage = new Image();
						sigilImage.src = "images/Sigils/"+sigil;
						//sigilImage.style.width=map.width*0.1+'px';
						sigilImage.align="top";
						sigilImage.width=160;
						sigilImage.height=197;
						flagImage.appendChild(sigilImage);

					
					}
					
					info.innerHTML+="</br><u><b>City Information</b></u>: </br>"+
					"Population: "+cityData.pop+"</br>"+
					"Army: "+cityData.army+"</br>"+
					"Order: "+cityData.myOrder+"</br>"+
					"Chaos: "+cityData.chaos+"</br>"+
					"Bank: "+cityData.bank+"</br>"
				}
				
				 


				info.innerHTML+="</br> <b><u>Connected areas</u></b>:"
				for(var l=0;l<routes[elements[k][2]].length-1;l++){
						info.innerHTML+="</br>"+routes[elements[k][2]][l];
				}
			}
		}
	}

		map.width = window.innerWidth*0.9;
		while(map.width>1000 || map.width*0.75 >window.innerHeight*0.85){
			map.width-=100;
			
		}
		map.height = map.width*0.75
		mapRatio = window.innerWidth/1280
		map.style.width=map.width+'px'	
	
	document.getElementsByClassName("row")[0].remove();


	function render(){
		
		if(window.innerWidth>1400){

			infoPanel.style.top='50%';
			infoPanel.style.left='92%';
			infoPanel.style.width='15%';
			infoPanel.style.height='80%';

			map.style.top='50%';			
		}else{
			map.style.top='12%';	
			infoPanel.style.top='110%';
			infoPanel.style.left='50%';
			infoPanel.style.width='100%';
			infoPanel.style.height='70%';		

			if(window.innerWidth>600){
				if(window.innerWidth>800){
				map.height = map.width*0.6
					
					map.style.top='45%';

				}else{
				map.height = map.width*0.75
				
				map.style.top='45%';			
				}
			}else{
				map.height = map.width*1.3
				map.style.top='40%';				
			}
		}
		
		
		scale = map.width/1280

		if(scale<0.6){
			scale=0.6;
		}
		
		if(xOff>0){
			xOff=0;
		}
		if(yOff>0){
			yOff=0;
		}
		
		if(xOff<-mapImg.width*scale+map.width){
			xOff = -mapImg.width*scale+map.width;
		}
	
		if(yOff<-mapImg.height*scale+map.height){
			yOff = -mapImg.height*scale+map.height;
		}
		
		elements=[];
		ctx.clearRect(0, 0, map.width, map.height);
		//draw map bg
		ctx.drawImage(mapImg,xOff,yOff,mapImg.width*scale,mapImg.height*scale);
		get=0;
		
		draw_area(sandImg,850,30,"Blasted Lands");
		draw_area(cityImg,1020,50,"Thakan'dar");
		draw_area(cityImg,1100,90,"Fal Dara");
		draw_area(rockImg,525,120,"World's End");
		draw_area(cityImg,680,130,"Maradon");
		draw_area(bushImg,760,130,"Plain of Lances");					
		draw_area(cityImg,840,130,"Chachin");	
		draw_area(cityImg,920,130,"Shol Arbela");
		draw_area(bushImg,1000,200,"Field of Merrilor");		
		draw_area(rockImg,1220,200,"Aiel Waste");		
		draw_area(mountainImg,1170,250,"Kinslayer's Dagger");	
		draw_area(cityImg,1220,320,"Rhuidean");	
		draw_area(cityImg,950,300,"Tar Valon");	
		draw_area(sandImg,825,250,"Black Hills");	
		draw_area(cityImg,1025,370,"Cairhien");
		draw_area(mountainImg,1170,400,"Spine of the World");	
		draw_area(sandImg,783,352,"Caralain Grass");	
		draw_area(mountainImg,550,352,"Mountains of Mist");	
		draw_area(bushImg,430,330,"Almoth Plain");
		draw_area(cityImg,340,300,"Bandar Eban");
		draw_area(treeImg,930,430,"Braem Wood");
		draw_area(treeImg,490,420,"Paerish Swar");	
		draw_area(cityImg,290,450,"Falme");	
		draw_area(cityImg,70,430,"Cantorin");
		draw_area(cityImg,550,470,"Emond's Field");	
		draw_area(cityImg,900,490,"Caemlyn");	
		draw_area(treeImg,580,520,"Forest of Shadows");		
		draw_area(cityImg,520,570,"Jehannah");
		draw_area(cityImg,320,570,"Tanchico");
		draw_area(caveImg,120,630,"Aryth Ocean");	
		draw_area(caveImg,315,780,"Windbiter's Finger");	
		draw_area(rockImg,380,680,"Shadow Coast");		
		draw_area(cityImg,515,660,"Amador");
		draw_area(cityImg,610,770,"Ebou Dar");
		draw_area(caveImg,660,900,"Sea of Storms");
		draw_area(cityImg,640,680,"Salidar");
		draw_area(mountainImg,680,560,"Garen's Wall");
		draw_area(cityImg,810,800,"Illian");
		draw_area(cityImg,770,580,"Lugard");
		draw_area(bushImg,900,740,"Plains of Maredo");
		draw_area(cityImg,975,710,"Tear");
		draw_area(sandImg,860,580,"Hills of Kintara");
		draw_area(cityImg,910,620,"Far Madding");
		draw_area(treeImg,1050,620,"Haddon Mirk");
		draw_area(cityImg,1230,680,"Stedding Shangtai");
		draw_area(cityImg,1220,760,"Mayene");
		draw_area(caveImg,1150,780,"Bay of Remara");
		
		get=1;
		
		draw_area(sandImg,850,30,"Blasted Lands",0,30);
		draw_area(cityImg,1020,50,"Thakan'dar");
		draw_area(cityImg,1100,90,"Fal Dara");
		draw_area(rockImg,525,120,"World's End");
		draw_area(cityImg,680,130,"Maradon");
		draw_area(bushImg,790,130,"Plain of Lances",0,-30);					
		draw_area(cityImg,840,130,"Chachin",0,80);	
		draw_area(cityImg,920,130,"Shol Arbela");
		draw_area(bushImg,1000,200,"Field of Merrilor");		
		draw_area(rockImg,1220,200,"Aiel Waste",-20,0);		
		draw_area(mountainImg,1170,250,"Kinslayer's Dagger");	
		draw_area(cityImg,1220,320,"Rhuidean",-10,0);	
		draw_area(cityImg,950,300,"Tar Valon");	
		draw_area(sandImg,825,250,"Black Hills");	
		draw_area(cityImg,1025,370,"Cairhien");
		draw_area(mountainImg,1170,400,"Spine of the World");	
		draw_area(sandImg,783,352,"Caralain Grass");	
		draw_area(mountainImg,550,352,"Mountains of Mist");	
		draw_area(bushImg,430,330,"Almoth Plain");
		draw_area(cityImg,340,300,"Bandar Eban");
		draw_area(treeImg,930,430,"Braem Wood");
		draw_area(treeImg,490,420,"Paerish Swar");	
		draw_area(cityImg,290,450,"Falme");	
		draw_area(cityImg,70,430,"Cantorin");
		draw_area(cityImg,550,470,"Emond's Field");	
		draw_area(cityImg,900,490,"Caemlyn");	
		draw_area(treeImg,580,520,"Forest of Shadows",30,0);		
		draw_area(cityImg,540,550,"Jehannah");
		draw_area(cityImg,320,570,"Tanchico");
		draw_area(caveImg,120,630,"Aryth Ocean");	
		draw_area(caveImg,315,780,"Windbiter's Finger");	
		draw_area(rockImg,380,680,"Shadow Coast");		
		draw_area(cityImg,515,660,"Amador");
		draw_area(cityImg,610,770,"Ebou Dar");
		draw_area(caveImg,660,900,"Sea of Storms");
		draw_area(cityImg,640,680,"Salidar");
		draw_area(mountainImg,680,560,"Garen's Wall");
		draw_area(cityImg,810,800,"Illian");
		draw_area(cityImg,770,580,"Lugard",0,30);
		draw_area(bushImg,900,740,"Plains of Maredo");
		draw_area(cityImg,975,710,"Tear");
		draw_area(sandImg,860,580,"Hills of Kintara");
		draw_area(cityImg,910,620,"Far Madding");
		draw_area(treeImg,1050,620,"Haddon Mirk",30,0);
		draw_area(cityImg,1230,680,"Stedding Shangtai",-50,0);
		draw_area(cityImg,1200,780,"Mayene",0,-20);
		draw_area(caveImg,1150,780,"Bay of Remara",0,30);
		requestAnimationFrame(render);
	}
	render();

</script>


<?php 
  $sortBy = 'id';
  $result = mysqli_query($db,"SELECT id, name, bank, pop, chaos, shoplvls, clan_scores, ruler, myOrder, army, isDestroyed FROM Locations ORDER BY $sortBy");  
  while ($loc = mysqli_fetch_array( $result ) )
  {
	  
	$society = mysqli_fetch_array(mysqli_query($db,"SELECT * FROM Soc WHERE name='$loc[ruler]' "));
	echo "<div style='display:none;' id='".$loc['name']."'>".json_encode($loc).'@'.json_encode($society['flag']).'@'.json_encode($society['sigil'])."</div>";

  }
  $loc = $char['location'];
  echo "<div style='display:none;' id=char>".json_encode($loc)."</div>";
	/*$routes=[];
	for ($i=0; $i<count($location_list); $i++)
	{
	   $routes[$location_list[$i]]=[];
	   $datalist =mysqli_query($db,"SELECT * FROM Routes WHERE start='$location_list[$i]'");
			
	   foreach ($datalist as $row) {
		   if(in_array($row['next'],$routes[$location_list[$i]])==false){
			array_push($routes[$location_list[$i]],$row['next']);
		   }
	   }
	}
	echo "<div style='display:none;' id='routes'>".json_encode($routes)."</div>";*/


include("footer.htm");

?>

<script>
	document.getElementsByTagName("h6")[0].remove();
</script>