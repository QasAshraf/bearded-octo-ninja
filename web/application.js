
gridWidth = grid[0].length;
gridHeight = grid.length;

canvasWidth = 1000; //window.innerWidth;
canvasHeight = 600; //(window.innerHeight / 100) * 80;


players = [];
velocity = [5,5];

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

  mazeBlockWidth = canvasWidth / gridWidth + 1;
  mazeBlockHeight = canvasHeight / gridHeight + 1;

  buildMaze();

  // Add the layer to the stage
  stage.add(layer);

  // Add test players
  addPlayer();

  // Test move
  // setTimeout(function() {
  //   movePlayer(players[0], 800, 500);
  // }, 1000);

  console.log(parseInt($("canvas").height()));

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
    fill: 'rgb(100,100,100)',
    stroke: 'rgb(100,100,100)',
    strokeWidth: 0
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

function movePlayer(playerIndex, x, y) {

  var anim = new Kinetic.Animation(function(frame) {
      var player = players[playerIndex];

      var distanceToX = player.getPosition().x - x;
      var distanceToY = player.getPosition().y - y;
      // console.log(distanceToX,distanceToY);
      if (distanceToX > 0) {
        player.move(velocity[0] * -1,0);
      } else if (distanceToX < 0) {
        player.move(velocity[0],0);
      }

      if (distanceToY > 0) {
        player.move(0, velocity[1] * -1);
      } else if (distanceToY < 0) {
        player.move(0, velocity[1]);
      }

      if (distanceToX === 0 && distanceToY === 0) {
        this.stop();
      }
  }, layer);
  anim.start();
}

function getRandomColor() {
    var letters = '0123456789ABCDEF'.split('');
    var color = '#';
    for (var i = 0; i < 6; i++ ) {
        color += letters[Math.round(Math.random() * 15)];
    }
    return color;
}