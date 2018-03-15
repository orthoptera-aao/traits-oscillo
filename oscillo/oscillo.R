library(tuneR)
library(seewave)

args = commandArgs(trailingOnly=TRUE)

recording_id <- args[1]
filename <- args[2];

#Read wave file
tryCatch({
  wave <- readWave(filename);
}, error = function(err) {
  print(err)
  quit("no", status=1, runLast=FALSE)
})

#normalise
tryCatch({
  wave <- normalize(wave);
}, error = function(err) {
  print(err)
  quit("no", status=2, runLast=FALSE)
})

#Plot
tryCatch({
  png(filename=paste0("modules/traits-oscillo/oscillo/",recording_id,".png"))
  plot(wave);
  dev.off()
}, error = function(err) {
  print(err
  )
  quit("no", status=3, runLast=FALSE)
})
