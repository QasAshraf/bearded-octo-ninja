
gridWidth = grid[0].length;
gridHeight = grid.length;

canvasWidth = 1000; //window.innerWidth;
canvasHeight = 650; //(window.innerHeight / 100) * 80;

players = [];
velocity = [2,2];

startX = 0;
startY = 0;

$(document).ready(function() {

  // Super shiny header
  var interval; 
  $("header").mouseover(function() {
    interval = setInterval(function() {
      $("h1").css({ color: getRandomColor() });
    }, 100);
  });
  $("header").mouseout(function() {
    clearInterval(interval);
    $("h1").css({ color: "rgb(80,80,80)" });
  });

  conn = new WebSocket('ws://109.109.137.94:8080');
  grid = conn.send( {"operation":"GAME","type":"NEW","recipient":"server","message":"newgame","sender":"steph","id":388542958} );
  
  conn.onopen = function(e) {
    console.log("Connection established!");
  };

  conn.onclose = function(e) {
    console.log("Connection closed!");
  };

  conn.onmessage = function(e) {
    console.log(e.data);
    splitData = e.data.split(" ");
    if (splitData[0] === "move") {
      movePlayer(parseInt(splitData[1]), parseInt(splitData[2]), parseInt(splitData[3]));
    } else if (splitData[0] === "add") {
      addPlayer();
    } else if (false/* Get grid data */) {
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
      addPlayer();
      addPlayer();
      addPlayer();
      addPlayer();
    }
  };

});

function buildMaze() {
  for (var i = 0; i < grid.length; i++) {
    for (var j = 0; j < grid[i].length; j++) {
      buildMazeBlock(i,j);
    };
  };
}

function buildMazeBlock(i,j) {
  var blockFill = ""
  if (grid[j][i] === " ") {
     return;
  } else if (grid[j][i] === "#") {
    blockFill = "rgb(100,100,100)";
  } else if (grid[j][i] === "s") {
    startX = i;
    startY = j;
    return;
  }

  var block = new Kinetic.Rect({
    x: i * mazeBlockWidth,
    y: j * mazeBlockHeight,
    width: mazeBlockWidth,
    height: mazeBlockHeight,
    fill: blockFill
  });

  if (grid[j][i] === "e") {
    console.log(j,i);
    setInterval(function() {
      block.setFill(getRandomColor());
      layer.draw();
    },100);
  }

  layer.add(block);
  layer.draw();
}

function addPlayer() {
  var newPlayer = new Kinetic.Rect({
    x: startX * mazeBlockWidth,
    y: startY * mazeBlockHeight,
    width: mazeBlockWidth,
    height: mazeBlockHeight,
    fill: getRandomColor()
  });

  players.push(newPlayer);

  stackPlayers(startX,startY);

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
    opacity: 1,
    onFinish: function() {
      stackedPlayers(i,j);
    }
  });

  tween.play();
}

function stackPlayers(i,j) {
  var stackedPlayers = [];
  var positionX = i * mazeBlockWidth;
  var positionY = j * mazeBlockHeight;
  for (var i = 0; i < players.length; i++) {
    if (players[i].getPosition().x === positionX
    &&  players[i].getPosition().y === positionY) {
      stackedPlayers.push(players[i])
    }
  }

  var stackRows = Math.ceil(players.length / 2);
  for (var i = 0; i < players.length; i++) {
    var row = Math.floor(i / 2);
    var column = i % 2;
    // Adjust x and y
    players[i].setPosition(
      players[i].getPosition().x + column * Math.round(mazeBlockWidth / 2),
      players[i].getPosition().y + row * Math.round(mazeBlockHeight / 2)
    );

    // Adjust width and height
    if (players[i].length % 2 == 0 || row != (stackRows - 1)) {
      players[i].setWidth(Math.round(mazeBlockWidth / 2));
    }
    player.setHeight(Math.round(mazeBlockHeight / stackRows));
  }

  layer.draw();

}

function whatIsWallWhatisNot() {
  $(".kineticjs-content").css({ background: "rgb(100,100,100)" });
  setTimeout(function() {
    $(".kineticjs-content").css({ background: "rgb(220,220,220)" });
  },2000);
}

function getRandomColor() {
    var letters = '0123456789ABCDEF'.split('');
    var color = '#';
    for (var i = 0; i < 6; i++ ) {
        color += letters[Math.round(Math.random() * 15)];
    }
    return color;
}