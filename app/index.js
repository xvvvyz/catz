const {app} = require('electron');
const windows = require('./windows');

app.on('ready', () => {
  windows.createMainWindow();
});

app.on('activate', () => {
  if (windows.main === null) windows.createMainWindow();
});

app.on('window-all-closed', () => {
  if (process.platform !== 'darwin') app.quit();
});
