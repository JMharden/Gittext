/* 存放全局变量 */
var _GLBConfig = {
    MAX_PLAYER_COUNT: 2,
    USER: [],
    GAME_START_EVENT: "gameStart",
    NEW_START_EVENT: "newStar",
    GAIN_SCORE_EVENT: "gainScore",
    PLAYER_POSITION_EVENT: "playerPosition",
    MAP_INIT: 'mapInit',
    USER_ALIKE: 'userAlike',
    SOCKET_SCORE: 'socketScore',
    SOCKET_CUBE: 'socketCube',
    GAME_READY: "gameReady",
    MAP_DIRECTION: [],//地图生成的方向,可能是二维数组
    MAP_LENGTH: 5,//地图生成方向的长度
    RAND_MAP_ARR: [],//随机地图数组
    RAND_MAP_ARR_S: [],//随机地图数组
    cubeX: 350,//用来存储当前生成马路的x位置
};
module.exports = _GLBConfig;
