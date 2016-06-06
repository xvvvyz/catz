import {app} from 'electron';
import windows from './windows';

app.on('ready', () => {
  windows.createMainWindow();
});

app.on('activate', () => {
  if (windows.main === null) windows.createMainWindow();
});

app.on('window-all-closed', () => {
  if (process.platform !== 'darwin') app.quit();
});
