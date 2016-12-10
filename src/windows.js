import {BrowserWindow} from 'electron';

const windows = module.exports = {
  createMainWindow: () => {
    let win = windows.main = new BrowserWindow({
      title: 'Catz',
      width: 600,
      minWidth: 500,
      height: 600,
      minHeight: 300,
      useContentSize: true
    });

    win.loadURL(`file://${__dirname}/index.html`);
    win.on('closed', () => windows.main = null);
  }
};
