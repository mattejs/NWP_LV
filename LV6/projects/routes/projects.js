var express = require('express'),
    router = express.Router(),
    mongoose = require('mongoose'),
    bodyParser = require('body-parser'),
    methodOverride = require('method-override');

router.use(bodyParser.urlencoded({ extended: true }))
router.use(methodOverride(function(req, res){
      if (req.body && typeof req.body === 'object' && '_method' in req.body) {
        var method = req.body._method
        delete req.body._method
        return method
      }
}))

router.route('/')
    .get(function(req, res, next) {
        mongoose.model('Project').find({}, function (err, projects) {
              if (err) {
                  return console.error(err);
              } else {
                  res.format({
                      html: function(){
                        res.render('projects/index', {
                              title: 'All Projects',
                              "projects" : projects
                          });
                    },
                    json: function(){
                        res.json(projects);
                    }
                });
              }     
        });
    })
    .post(function(req, res) {
        var name = req.body.name;
        var description = req.body.description;
        var price = req.body.price;
        var finishedTasks = req.body.finishedTasks;
        var startDate = req.body.startDate;
        var endDate = req.body.endDate;
        mongoose.model('Project').create({
            name : name,
            description : description,
            price : price,
            finishedTasks : finishedTasks,
            startDate : startDate,
            endDate : endDate
        }, function (err, project) {
              if (err) {
                  res.send("There was a problem adding the information to the database.");
              } else {
                  console.log('POST creating new project: ' + project);
                  res.format({
                    html: function(){
                        res.location("projects");
                        res.redirect("/projects");
                    },
                    json: function(){
                        res.json(project);
                    }
                });
              }
        })
    });

router.get('/new', function(req, res) {
    res.render('projects/new', { title: 'Add New Project' });
});

router.param('id', function(req, res, next, id) {
    console.log('validating ' + id + ' exists');
    mongoose.model('Project').findById(id, function (err, project) {
        if (err) {
            console.log(id + ' was not found');
            res.status(404)
            var err = new Error('Not Found');
            err.status = 404;
            res.format({
                html: function(){
                    next(err);
                 },
                json: function(){
                       res.json({message : err.status  + ' ' + err});
                 }
            });
        } else {
            req.id = id;
            next(); 
        } 
    });
});

router.route('/:id')
  .get(function(req, res) {
    mongoose.model('Project').findById(req.id, function (err, project) {
      if (err) {
        console.log('GET Error: There was a problem retrieving: ' + err);
      } else {
        console.log('GET Retrieving ID: ' + project._id);
        var startDate = project.startDate.toISOString();
        var endDate = project.endDate.toISOString();              
        startDate = startDate.substring(0, startDate.indexOf('T'))
        endDate = endDate.substring(0, endDate.indexOf('T'))
        res.format({
          html: function(){
              res.render('projects/show', {
                "startDate" : startDate,
                "endDate" : endDate,
	            "project" : project
              });
          },
          json: function(){
              res.json(project);
          }
        });
      }
    });
  });

router.route('/:id/addmember')
    .get(function(req, res) {
        mongoose.model('Project').findById(req.id, function (err, project) {
        if (err) {
            console.log('GET Error: There was a problem retrieving: ' + err);
        } else {
            console.log('GET Retrieving ID: ' + project._id);
            res.format({
            html: function(){
                res.render('projects/addmember', {
                    "project" : project
                });
            },
            json: function(){
                res.json(project);
            }
            });
        }
        })
           
  })
  .put(function(req, res) {
    var members = req.body.members;
    mongoose.model('Project').findById(req.id, function (err, project) {
        mongoose.model('Project').findOneAndUpdate({_id: project._id},
            {$push: {members: members}
        }, function (err, projectID) {
          if (err) {
              res.send("There was a problem updating the information to the database: " + err);
          } 
          else {
            console.log('MEMBERS UPDATED FOR ' + project._id + project.members);
                  res.format({
                      html: function(){
                           res.redirect("/projects/" + project._id);
                     },
                    json: function(){
                           res.json(project);
                     }
                  });
           }
        })
    });
})

router.route('/:id/edit')
	.get(function(req, res) {
	    mongoose.model('Project').findById(req.id, function (err, project) {
	        if (err) {
	            console.log('GET Error: There was a problem retrieving: ' + err);
	        } else {
	            console.log('GET Retrieving ID: ' + project._id);
              var startDate = project.startDate.toISOString();
              var endDate = project.endDate.toISOString();              
              startDate = startDate.substring(0, startDate.indexOf('T'))
              endDate = endDate.substring(0, endDate.indexOf('T'))
	            res.format({
	                html: function(){
	                       res.render('projects/edit', {
	                          title: 'Project' + project._id,
                            "startDate" : startDate,
                            "endDate" : endDate,
	                        "project" : project
	                      });
	                 },
	                json: function(){
	                       res.json(project);
	                 }
	            });
	        }
	    });
	})
	.put(function(req, res) {
	    var name = req.body.name;
        var description = req.body.description;
        var price = req.body.price;
        var finishedTasks = req.body.finishedTasks;
        var startDate = req.body.startDate;
        var endDate = req.body.endDate;
	    mongoose.model('Project').findById(req.id, function (err, project) {
	        project.update({
	            name : name,
                description : description,
                price : price,
                finishedTasks : finishedTasks,
                startDate : startDate,
                endDate : endDate
	        }, function (err, projectID) {
	          if (err) {
	              res.send("There was a problem updating the information to the database: " + err);
	          } 
	          else {
	                  res.format({
	                      html: function(){
	                           res.redirect("/projects/" + project._id);
	                     },
	                    json: function(){
	                           res.json(project);
	                     }
	                  });
	           }
	        })
	    });
	})
	.delete(function (req, res){
	    mongoose.model('Project').findById(req.id, function (err, project) {
	        if (err) {
	            return console.error(err);
	        } else {
	            project.remove(function (err, project) {
	                if (err) {
	                    return console.error(err);
	                } else {
	                    console.log('DELETE removing ID: ' + project._id);
	                    res.format({
	                          html: function(){
	                               res.redirect("/projects");
	                         },
	                        json: function(){
	                               res.json({message : 'deleted',
	                                   item : project
	                               });
	                         }
	                      });
	                }
	            });
	        }
	    });
	});

module.exports = router;