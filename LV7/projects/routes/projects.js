var express = require('express');
var bodyParser = require('body-parser');
var methodOverride = require('method-override');
var mongoose = require('mongoose');
var router = express.Router();

const project = mongoose.model('Project');
const user = mongoose.model('User');

router.use(bodyParser.urlencoded({ extended: true }))
router.use(methodOverride((req, res) => {
      if (req.body && typeof req.body === 'object' && '_method' in req.body) {
        var method = req.body._method;
        delete req.body._method;
        return method;
      }
}));

router.get('/', async (req, res, next) => {
    if(!req.session['userId']) {
        return res.redirect('/users/login');
    }
    const allProjects = await project.find({ archived: false });
    res.format({
      html: () => res.render('projects/index', { title: 'All projects', projects: allProjects }),
      json: () => { res.json(allProjects) }
    });
});

router.get('/owner', async (req, res, next) => {
    if(!req.session['userId']) {
        return res.redirect('/users/login');
    }
    const allProjects = await project.find({ owner: req.session['userId'], archived: false });
    res.format({
      html: () => res.render('projects/index', { title: 'Your projects (Author)', projects: allProjects }),
      json: () => { res.json(allProjects) }
    });
});

router.get('/member', async (req, res, next) => {
    if(!req.session['userId']) {
        return res.redirect('/users/login');
    }
    const allProjects = await project.find({ archived: false });
    const currentUser = await user.findById(req.session['userId'])
    const projects = [];

    allProjects.forEach((project) => {
        if(project.members.indexOf(currentUser.mail) != -1) {
            projects.push(project);
        }
    });

    res.format({
      html: () => res.render('projects/index', { title: 'Your projects (Member)', projects: projects }),
      json: () => { res.json(allProjects) }
    });
});

router.get('/archive', async (req, res, next) => {
    if(!req.session['userId']) {
        return res.redirect('/users/login');
    }
    const allProjects = await project.find({ archived: true });
    const currentUser = await user.findById(req.session['userId'])
    const projects = [];

    allProjects.forEach((project) => {
        if(project.members.indexOf(currentUser.mail) != -1  || project.owner == req.session['userId']) {
            projects.push(project);
            console.log(project);
        }
    });

    res.format({
      html: () => res.render('projects/index', { title: 'Project Archive', projects: projects }),
      json: () => { res.json(allProjects) }
    });
});

router.post('/', async (req, res) => {
    const savedProject = await project.create(req.body);
    console.log(savedProject);
    console.log(`[POST] Project saved!`);

    res.format({
        html: () => {
            res.location('projects');
            res.redirect('/projects');
        },
        json: () => res.json(savedProject)
    });
});

router.get('/new', async (req, res) => {
    if(!req.session['userId']) {
        return res.redirect('/users/login');
    }
    const users = await user.find({});
    res.render('projects/new', { 
        title: 'Add new project',
        userId: req.session['userId']
    });
});

router.get('/show/:id/', async (req, res) => {
    if(!req.session['userId']) {
        return res.redirect('/users/login');
    }
    const loadedProject = await project.findById(req.params.id);
    res.render('projects/show', { title: 'Project', project: loadedProject });
});

router.delete('/:id/', async (req, res) => {
    if(!req.session['userId']) {
        return res.redirect('/users/login');
    }
    const deletedProject = await project.findByIdAndRemove(req.params.id)
    console.log(`[DELETE] Project deleted!`);
    res.format({
        html: () => res.redirect('/projects'),
        json: () => res.json({ message: 'Project deleted', item: deletedProject })
    });
});

router.get('/:id/edit', async (req, res) => {
    if(!req.session['userId']) {
        return res.redirect('/users/login');
    }
    let users = await user.find({});
    const loadedProject = await project.findById(req.params.id);

    let viewType = 0;
    const member = await user.findById(req.session['userId']);

    if(loadedProject.owner == req.session['userId'])
    {
        viewType = 1;
    }

    for(let i = 0; i < users.length; i++) {
        loadedProject.members.forEach(m => {
            if(m == member.mail) {
                viewType = 2;
            }
        });
    }

    res.render('projects/edit', { 
        title: 'Edit project',
        project: loadedProject,
        users: users.filter(u => u._id != req.session['userId']),
        viewType: viewType
    });
});

router.put('/:id/edit', async (req, res) => {
    
    req.body.archived = req.body.archived == 'on' ? true : false;
    const editedProject = await project.findByIdAndUpdate(req.params.id, req.body);
    console.log(req.params.id);
    console.log(req.body);
    console.log(`[PUT] Project edited!`);

    res.format({
        html: () => res.redirect('/projects'),
        json: () => res.json({ message: 'Project edited', item: editedProject })
    });
});

router.get('/:id/addmember', async (req, res) => {
    if(!req.session['userId']) {
        return res.redirect('/users/login');
    }
    const users = await user.find({});
    const projects = await project.findById(req.params.id);
    res.render('projects/addmember', { 
        title: 'Add member', 
            users: users.filter(u => u._id != req.session['userId']),
            userId: req.session['userId'],
            projects : projects
        });
           
  })
  .put('/:id/addmember', async (req, res) => {
    
    const newMember = await project.findByIdAndUpdate({_id: req.params.id}, 
        {$push: {members: req.body.members}});
    console.log(`[PUT] New member added to project: ${newMember._id}!`);

    res.format({
        html: () => res.redirect('/projects'),
        json: () => res.json({ message: 'Project edited', item: newMember })
    });
});

module.exports = router;