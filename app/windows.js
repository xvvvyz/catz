import {BrowserWindow} from 'electron';

function createMainWindow() {
  let win = windows.main = new BrowserWindow({
    title: 'Catz',
    width: 500,
    minWidth: 500,
    height: 600,
    minHeight: 300,
    titleBarStyle: 'hidden-inset',
    backgroundColor: '#fff',
    darkTheme: true,
    useContentSize: true
  });

  win.loadURL(`file://${__dirname}/index.html`);
  win.on('closed', () => windows.main = null);
}

let windows = module.exports = {
  createMainWindow
};
