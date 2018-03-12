library(tuneR)
library(seewave)
library(stringr)

args = commandArgs(trailingOnly=TRUE)

recording_id <- args[1]
species <- args[2]
filename <- args[3];


wave <- readWave(filename);

#normalise
wave <- normalize(wave);

png(filename=paste0("modules/oscillo/",recording_id,".png"))
plot(wave);
dev.off()