var mongoose = require('mongoose');  
var projectSchema = new mongoose.Schema({  
  name: String,
  description: String,
  price: Number,
  finishedTasks: String,
  members: [],
  startDate: { type: Date, default: Date.now },
  endDate: { type: Date, default: Date.now },
});
mongoose.model('Project', projectSchema);