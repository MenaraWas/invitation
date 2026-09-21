import express from 'express';
import QRCode from 'qrcode';
import cors from 'cors';
import { Boom } from '@hapi/boom';
import makeWASocket, {
  DisconnectReason,
  useMultiFileAuthState,
  fetchLatestBaileysVersion,
  makeCacheableSignalKeyStore,
} from '@whiskeysockets/baileys';

const app = express();
const port = process.env.WA_PORT || 3001;
const SESSION_DIR = './.wa-auth';
const silentLogger = {
  level: 'silent',
  child() {
    return this;
  },
  trace: () => {},
  debug: () => {},
  info: () => {},
  warn: () => {},
  error: () => {},
  fatal: () => {},
};

app.use(cors());
app.use(express.json());

let qrCode = null;
let sock = null;
let isConnected = false;

async function startWa() {
  const { state, saveCreds } = await useMultiFileAuthState(SESSION_DIR);
  const { version } = await fetchLatestBaileysVersion();

  const client = makeWASocket({
    version,
    logger: silentLogger,
    printQRInTerminal: false,
    auth: {
      creds: state.creds,
      keys: makeCacheableSignalKeyStore(state.keys, silentLogger),
    },
    browser: ['Chrome', 'Safari', '1.0'],
  });

  sock = client;

  client.ev.on('connection.update', async (update) => {
    const { connection, lastDisconnect, qr } = update;

    if (qr) {
      qrCode = await QRCode.toDataURL(qr);
      isConnected = false;
      console.log('QR generated');
    }

    if (connection === 'open') {
      isConnected = true;
      qrCode = null;
      console.log('WhatsApp connected');
    }

    if (connection === 'close') {
      const shouldReconnect = (lastDisconnect?.error || new Boom('Unknown error')).output?.statusCode !== DisconnectReason.loggedOut;
      isConnected = false;

      if (shouldReconnect) {
        startWa();
      }
    }

    if (connection === 'open') {
      await saveCreds();
    }
  });

  client.ev.on('creds.update', saveCreds);
}

app.get('/wa/status', (req, res) => {
  res.json({
    connected: isConnected,
    qr: qrCode,
    sessionDir: SESSION_DIR,
  });
});

app.post('/wa/send', async (req, res) => {
  if (!sock || !isConnected) {
    return res.status(400).json({ error: 'WhatsApp belum login' });
  }

  const { to, message } = req.body;

  if (!to || !message) {
    return res.status(400).json({ error: 'Parameter to dan message wajib diisi' });
  }

  try {
    await sock.sendMessage(to.includes('@s.whatsapp.net') ? to : `${to}@s.whatsapp.net`, { text: message });
    return res.json({ success: true });
  } catch (error) {
    console.error(error);
    return res.status(500).json({ error: 'Gagal kirim WA' });
  }
});

app.get('/wa/qr', async (req, res) => {
  if (!qrCode) {
    return res.status(404).json({ message: 'QR belum tersedia' });
  }

  res.json({ qr: qrCode });
});

app.listen(port, async () => {
  console.log(`WhatsApp service running on http://localhost:${port}`);
  await startWa();
});
