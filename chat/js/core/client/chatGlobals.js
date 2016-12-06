/** global variables*/
var suppressAlreadyInRoomMsg = true; //used in chatHandler.js
var firstLoad = true; //see onRoomJoinCall() in chatHandler.js
var serverURL = webRootPath + "ajaxphp/ajax_chat_handler.php"; 
var messageCache = []; 
var openConversationsIDStack = []; //array of ids of open chat conversations
var openConversationsStack = []; //array of objects containing id and title of open chat conversations
var closedWindowId = 0; //id of most recently closed window
var currRoom; //the room the user is currently in
var roomToMoveTo; //the room the user is moving to
var currDisplayedUsers = [];
var currDisplayedMessages = []; //array of all messages currently being viewed in the current users window(s), used by formatMessages() 
var readMessagesIds = []; //array of messages sent to the current user that the user has read, used by updateReadMessages() in closedWindowManager.js
var roomIds = []; //used by populateRoomsArray()
var rooms = []; //usage ex.: [{room_id: yourRoomId, room_name: yourRoomName}, {room_id: yourRoomId, room_name: yourRoomName}, ...]
var lastMessageId = -1;
var interval = 1000; 
var minimumZIndex = '11';
var maximumZIndex = '15';
var theme;
var themeColor;
var themePath; //path to specific themes like 'light', 'dark', etc.
var themeStylePath; //path to theme-specific style sheet
var emoteImgPath; //path to emoticons /smileys
var statusImgPath; //path to images that represent user status
var styleImgPath; //path to the window styling images
var textEditImgPath; //path to images for editing text e.g 'bold', 'underline', etc
var bgcolor;
var buddyIds = []; //used by populateBuddiessArray()
var buddies = [{'id': 0, 'status': true}]; 
var buddyOnlineIds = [];
var buddiesOnline = []; 
var errorDiv = ''; //holds the error display div of the current message window
var messageWindow = ''; //holds the current IM or chat room window
var usersDetails = []; //holds the user details object returned by the call to getUserData
var rePopulateList = true; //false; //determines whether(true) or not(false) to clear the chat room users list b4 updating it
var justJoinedRoom = true; //false; //did the current user just join the room or has he been previously
var IMWinLocation = {'positioning': 'fixed', 'bottom': '0px', 'right': '5px'} //sets the location of the parent/main instant messenger window
var chatRoomWinLocation = {'positioning': 'fixed', 'right': '220px', 'bottom': '0px'} //sets the location of the chat room window
var PMWinLocation = {'positioning': 'fixed', 'right': '220px', 'bottom': '0px'} //sets the locations of each of the private messages windows
function getChatUserId(){return uid}