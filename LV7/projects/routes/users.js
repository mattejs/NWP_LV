var express = require('express');
var bodyParser = require('body-parser');
var methodOverride = require('method-override');
var mongoose = require('mongoose');
var router = express.Router();

const users = mongoose.model('User');

router.use(bodyParser.urlencoded({ extended: true }))
router.use(methodOverride((req, res) => {
      if (req.body && typeof req.body === 'object' && '_method' in req.body) {
        var method = req.body._method;
        delete req.body._method;
        return method;
      }
}));

router.get('/new', (req, res) => {
  res.render('users/new', { title: 'Registration' });
});

router.post('/', async (req, res) => {
  const savedUser = await users.create(req.body);
  console.log(`[POST] New user registred with e-mail: ${req.body.mail}!`);

  res.format({
      html: () => {
          res.location('users');
          res.redirect('/projects');
      },
      json: () => res.json(savedUser)
  });
});

router.get('/login', (req, res) => {
  res.render('users/login', { title: 'Login' });
});

router.post('/login', async (req, res) => {
  const user = await users.findOne({ mail: req.body.mail, password: req.body.password });
  if(!user) {
    return res.status(401).json({ error: "User does not exist" });
  }
  req.session.userId = user._id;

  res.format({
    html: () => {
      res.location('projects');
      res.redirect('/projects');
    },
    json: () => res.json(savedUser)
  });
  console.log(`[POST] User login with e-mail: ${req.body.mail}!`);
});

module.exports = router;