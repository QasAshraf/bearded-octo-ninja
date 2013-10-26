
gridWidth = grid[0].length;
gridHeight = grid.length;

canvasWidth = 1000; //window.innerWidth;
canvasHeight = 600; //(window.innerHeight / 100) * 80;


players = [];
velocity = [2,2];

$(document).ready(function() {

  // setInterval(function() {
  //   $("h1").css({ color: getRandomColor() });
  // }, 100);

  conn = new WebSocket('ws://109.109.137.94:8080');
  conn.onopen = function(e) {
    console.log("Connection established!");
  };

  conn.onmessage = function(e) {
    console.log(e.data);
    splitData = e.data.split(" ");
    if (splitData[0] === "move") {
      movePlayer(parseInt(splitData[1]), parseInt(splitData[2]), parseInt(splitData[3]));
    } else if (splitData[0] === "add") {
      addPlayer();
    }
  };

  conn.onclose = function(e) {
    console.log("Connection closed!");
  };

  stage = new Kinetic.Stage({
    container: "game-container",
    width: canvasWidth,
    height: canvasHeight
  });
  layer = new Kinetic.Layer();

  mazeBlockWidth = Math.round(canvasWidth / gridWidth);
  mazeBlockHeight = Math.round(canvasHeight / gridHeight);

  buildMaze();

  // Add the layer to the stage
  stage.add(layer);

  // Add test players
  addPlayer();

  // Test move
  // setTimeout(function() {
  //   movePlayer(players[0], 800, 500);
  // }, 1000);

});

function buildMaze() {
  for (var i = 0; i < grid.length; i++) {
    for (var j = 0; j < grid[i].length; j++) {
      if (grid[j][i] === "#") {
        buildMazeBlock(i,j);
      }
    };
  };
}

function buildMazeBlock(i,j) {
  var block = new Kinetic.Rect({
    x: i * mazeBlockWidth,
    y: j * mazeBlockHeight,
    width: mazeBlockWidth,
    height: mazeBlockHeight,
    fill: 'rgb(100,100,100)'
  });
  layer.add(block);
  layer.draw();
}

function addPlayer() {
  var newPlayer = new Kinetic.Rect({
    x: 0,
    y: 0,
    width: mazeBlockWidth,
    height: mazeBlockHeight,
    fill: getRandomColor()
  });

  players.push(newPlayer);

  // add the shape to the layer
  layer.add(newPlayer);

  layer.draw();
}

function movePlayer(playerIndex, i, j) {
  console.log(players[playerIndex]);

  var tween = new Kinetic.Tween({
    node: players[playerIndex], 
    duration: 1,
    x: i * mazeBlockWidth,
    y: j * mazeBlockHeight,
    opacity: 1
  });

  tween.play();
}

function getRandomColor() {
    var letters = '0123456789ABCDEF'.split('');
    var color = '#';
    for (var i = 0; i < 6; i++ ) {
        color += letters[Math.round(Math.random() * 15)];
    }
    return color;
}