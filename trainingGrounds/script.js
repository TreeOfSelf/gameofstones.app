exp_up = function(level){
    return( 10 * (level-1) + 150);
}

total_xp = function(){
    let total_xp = 0;
    let xp_array = [150];
    for(var k=1;k<=150;k++){
        total_xp+=exp_up(k);
        xp_array.push(total_xp);
    }
    return xp_array; 
}

function getData(doDays=false){
    let maxLevel = document.getElementById("maxLevel").value;
    let groundsLevel = document.getElementById("groundsLevel").value;

    let xpNeededTable = total_xp();
    let timeToLevelUp = [];
    let xData = []
    for(var k=1;k<=Math.floor(maxLevel*0.75);k++){
        xData.push(k);
        let xpNeeded = xpNeededTable[k];
        let distanceToCap = exp_up(maxLevel);
        let scalingFactor = 0.25; 
    
        // Calculate the XP based on the scaling factor and remaining distance
        let xp = scalingFactor * distanceToCap * (groundsLevel/10);
        let hoursNeeded = xpNeeded / xp;

        if(!doDays){
            timeToLevelUp.push(hoursNeeded);
        }else{
            timeToLevelUp.push(hoursNeeded/24);
        }
        
    }

    let innerHTML = "";
    for( var k=0;k<xData.length;k++){
        innerHTML += xData[k] +": "+ timeToLevelUp[k].toFixed(2) + "</br>";
    }
    let data = document.getElementById("data");
    data.innerHTML = innerHTML;

    console.log(timeToLevelUp);
	plot = document.getElementById('plot');
	Plotly.newPlot( plot, [{
	x: xData,
	y: timeToLevelUp }], {
	margin: { t: 0 } } );
}


let testHours = document.getElementById("testHours");
let testDays = document.getElementById("testDays");
testHours.onclick = function(){
    getData();
}
testDays.onclick = function(){
    getData(true);
}