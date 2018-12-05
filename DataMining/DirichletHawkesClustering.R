

###data loading，install packages############################################
###################################
Needed <- c("tm", "SnowballC", "RColorBrewer", "ggplot2", "wordcloud", "biclust", "cluster", "igraph", "fpc")   
install.packages(Needed, dependencies=TRUE) 
install.packages("Rcampdf", repos = "http://datacube.wu.ac.at/", type = "source") 
library(tm)
library(SnowballC)

cname<-file.path("~","Desktop","News")   ##word directory set different between MAC and PC!!
cname
dir(cname)


#################transform text document to corpus##################################

data=DirSource(cname,encoding = "latin1")     #latin1 , remove other symbols
docs<-Corpus(data,readerControl=list(reader=readPlain))
summary(docs)   #check


##label documents arrival time###
dates<-sample(seq.Date(as.Date("2015-08-01"),as.Date("2016-03-01"),length.out=100),1205,replace=T)
newdates<-sample(seq.Date(as.Date("2015-09-12"),as.Date("2016-01-14"),length.out=100),1205,replace=T)
index<-order(dates)
for (i in 1:1205)
{
   temp<-index[i]
newdates[i]<-dates[temp]

}
newdates

for(i in 1:length(docs))
{
	meta(docs[[i]],tag="datetimestamp")<-newdates[i]
}

summary(docs)   #check time stream


#######converting to lowercase######do this one first!!!!!!tolower easy to fail after other process

docs<-tm_map(docs,content_transformer(tolower))
summary(docs)     #check 



#########remove numbers############


docs<-tm_map(docs,removeNumbers)
summary(docs)


#####remove stop words#######

docs<-tm_map(docs,removeWords,stopwords("english"))
docs<-tm_map(docs,removeWords,c("say","state","for","reuters"))
summary(docs)



##combine words

#for(j in seq(docs))
#{
#	docs[[j]]<-gsub("new york","QDA",docs[[j]])
#	docs[[j]]<-gsub("los angelos","QDA",docs[[j]])
	#docs[[j]]<-gsub("@"," ",docs[[j]])
	#docs[[j]]<-gsub("\\|"," ",docs[[j]])
	#docs[[j]]<-gsub("?"," ",docs[[j]])
#}

#inspect(docs[1])
###stemming document#########
##result is not good, skip stemming words#########



#######remove whitespace########
docs<-tm_map(docs,stripWhitespace)
summary(docs)


#####TD Matrix######

dtm<-DocumentTermMatrix(docs,control = list(removePunctuation = TRUE, stopwords = TRUE)
)
tdm<-TermDocumentMatrix(docs)

freq<-colSums(as.matrix(dtm))
length(freq)
ord<-order(freq)

dtms<-removeSparseTerms(dtm,0.95)
inspect(dtms)

dtm<-dtms

###dtm is the main object for DHP#########
##########count freq, general description for frequent words##########

freq[tail(ord)]
head(table(freq),20)
tail(table(freq),20)
freq<-colSums(as.matrix(dtms))
freq
freq<-sort(colSums(as.matrix(dtm)),decreasing=TRUE)
freq
head(freq,10)
test=head(freq,10)


findFreqTerms(dtm,lowfreq=500)
wf<-data.frame(word=names(freq),freq=freq)
head(wf)

#########################################simple frequency check, remove more stopping words###
library(ggplot2)
p<-ggplot(subset(wf,freq>1000),aes(word,freq))
p<-p+geom_bar(stat="identity")
p<-p+theme(axis.text.x=element_test(angle=45,hjust=1))
p



##################Dirichlet Clustering for density######
#########max cluster number determined by Dirichilet parameter##############

install.packages("dpmixsim")
library(dpmixsim)
freq
newfreq=as.vector(freq)
scalefreq=prescale(newfreq)
maxiter<-400
rec<-300
ngrid<-50

#######M=0.1,Dirichilet distribution parameter########



########fix function parameter to plot trigerring kernel intensity##########

postdpmixciz <-
function(x, res, kmax=30, rec=300, ngrid=200, plot=TRUE)
{
    ## relabel to keep values in sequence
    relabel <-
    function(z)
    {
        u <- sort(unique(z))
        v <- 1:length(u)
        for(k in 1:length(u)) {
            w <- which(z == u[k])
            z[w] <- v[k]
        }
        invisible(z)
    }
    krec   <- res$krec
    wrec   <- matrix(res$w,ncol=kmax, byrow=TRUE)
    phirec <- matrix(res$phirec,ncol=kmax, byrow=TRUE)
    varrec <- matrix(res$varrec,ncol=kmax, byrow=TRUE)
    ## Histogram for k
    khist <- numeric(kmax)
    for (i in 1:length(krec)) {
        khist[krec[i]] <- khist[krec[i]] + 1  
    }
    kfreq <- which(khist == max(khist))[1] # take the first if more than one
    cat("most frequent k (kfreq):",kfreq,"\n") 
    khist <- khist/length(krec)
    ##------------------------------------------
    par(mfrow=c(2,2),ask=TRUE)
    if(plot) {
       # par(mfrow=c(2,2))
        narg <- range(which(khist != 0))
       # par(mar=c(5, 1, 2, 1) + 0.1)
        barplot(khist[narg[1]:narg[2]], names.arg=narg[1]:narg[2], main="Number of components", axes=FALSE, xlab="k")
         par(mfrow=c(1,1), ask=TRUE)
    }
    ##------------------------------------------
    ## Density estimate using kfreq simulated components at each iteration
    ## cidens     <- numeric(ngrid)
    t  <- seq(0,1,length=ngrid)
    cidens <- matrix(0, nrow=kfreq, ncol=ngrid)
    ydens <- numeric(ngrid)
    ## muk <- NULL
    ## vark <- NULL
    km <- 0
    for(i in 1:length(krec)) {
        nk  <- krec[i]
        ## densities for simulations with kfreq components
        ## !!! beware label switching
        ## !!! guarantee of equal number of components (with sort) is not enough 
        ## !!! many mu simulated values are centered on different ranges
        if(nk == kfreq) {
            w   <- wrec[i,1:nk] 
            mu  <- phirec[i,1:nk]
            var <- varrec[i,1:nk]
            km <- km + 1;
            imu <- sort(mu,ind=TRUE)
            loc <- imu$ix
            ## reordering of mu simulated values
            ## muk <- cbind(muk,mu)
            ## vark <- c(vark,var)
            mu <- mu[loc]
            w  <- w[loc]
            v  <- var[loc]
            ## muk <- cbind(muk,mu)
            dens     <- matrix(0, nrow=nk, ncol=ngrid) 
            for (j in 1:nk){
              # dens[j,] <- dnorm(t, mu[j], sqrt(var)) # unique var
              dens[j,] <- dnorm(t, mu[j], sqrt(v[j])) 
            }
            ydens  <- ydens + as.vector(w%*%dens)    # y density for k=kfreq
            cidens <- cidens + dens*w                # component densities
        }
    }
    cidens    <- cidens/km
    ydens     <- ydens/km
    cat("n.iterations with kfreq:",km,"\n") 
    ##-------------------------
    if(plot) {
        ## Density plots
       # par(mar=c(5, 3, 2, 1) + 0.1)
        hist(x,prob=TRUE,br="Freedman-Diaconis", xlab="scaled intensity", ylab="", main="Estimated histogram")
        lines(t,ydens)
        # par(mfrow=c(1,1), ask=TRUE)
        # lines(t,ydens,col="red")
        ##--------------
       # par(mar=c(5, 3, 2, 1) + 0.1)
        ##
        for(k in 1:kfreq) {
            if(k==1)
            
            
                  plot(t, cidens[k,], ty="l", col=k ,lwd=2,xlim=c(0,1), ylim=c(0,10),
                    xlab="scaled intensity", ylab="", main="Density estimates", axes=FALSE)
            else
                       
                lines(t, cidens[k,], col=k,lwd=2)
                 par(mfrow=c(1,1), ask=TRUE)
        }
        axis(1, seq(0,0.6,by=0.1), seq(0,0.6,by=0.1))
        axis(2, seq(0,6,by=1), seq(0,6,by=1))
    }
    dev     <- dev.cur()
    #--------------------------------------------
    # z values : evaluate from w-,mu-,var-results 
    zall                 <- matrix(0,nrow=kfreq,ncol=ngrid) 
    for(k in 1:kfreq) {
        zi                 <- cidens[k,]/ydens 
        zall[k,]     <- zi
        par(mfrow=c(3,3), ask=TRUE)
        if(plot) {
        	
            plot(t,zi,lty=1,col=k,ty="l",lwd=3,xlim=c(0,1),ylim=c(0,1),
              xlab="Time",ylab="Intensity", main="Trigerring Kernel")
             
            # !!! applying round of zi may leave holes in z
            # lines(t,round(zi),lty="dotted",col=k)
             #par(new=TRUE)
           
        }
                    
        
    }
    for(k in 1:kfreq) {
        zi                 <- cidens[k,]/ydens 
        zall[k,]     <- zi
        if(plot) {
        	par(mfrow=c(2,2), ask=TRUE)
            plot(t,zi,lty=1,col=k,ty="l",lwd=1,xlim=c(0,1),ylim=c(0,1),
              xlab="Time",ylab="Intensity", main="Cluster Partition")
             
            # !!! applying round of zi may leave holes in z
            # lines(t,round(zi),lty="dotted",col=k)
              par(new=TRUE)
           
        }
                    
        
    }
    # better: choosing z by the most probable zi
    choose   <- function(x) { return( which(x == max(x))) }
    z        <- apply(zall,2,choose)
    z <- relabel(z)
    #--------------------------------------------
    if(plot) { 
        points(t,rep(0,ngrid),col=z,pch=20)
        # apply also to previous plot
        cdev <- dev.cur()
        dev.set(dev)
        for(i in 1:max(z)) {
            jj <- range(which(z == i))
            lines(t[jj], c(-0.18, -0.18), lty=i+1, lwd=2.2)
            points(t[jj], c(-0.18, -0.18), pch=22)
        }
        dev.set(dev) # restore dev
        par(mfrow=c(1,1), ask=TRUE)
    }
invisible(z)
}

###################end #################################################

##get optimal number of cluster by mixed model##
res <- dpmixsim(scalefreq, M=1, a=1, b=0.1, upalpha=1, maxiter=maxiter, rec=rec,nclinit=NA)
z <- postdpmixciz(x=newfreq, res=res, rec=rec,ngrid=ngrid, plot=T)



#########remove zero matrix for LDA process!!!!!########################
rowTotals <- apply(dtm, 1, sum)
dtm2 <- dtm[rowTotals>0,]

install.packages("topicmodels")
library(topicmodels)

####################Dirichilet alpha 1, MCMC Gibbs sampleing method#########
number_topic<-9
dtm_LDA <- LDA(dtm2, method="Gibbs",control=list(alpha=1,estimate.beta=TRUE),k=number_topic)

#can vew each topic belong to which document

Topic<-topics(dtm_LDA)

Terms<-terms(dtm_LDA,100)
#Terms[,1:4]



#last step is to plot a wordcloud################################################

library(wordcloud)
library(devtools)
install_github("kasperwelbers/corpus-tools")
library(corpustools)


Terms1<-Terms[,1]
freqs<-posterior(dtm_LDA)$terms[1,]

freqs<-sort(freqs,decreasing=T)
terms<-names(freqs)

select=order(-freqs)[1:100]
terms=terms[select]
freqs=freqs[select]

wordcloud(terms,freqs,random.order=F,random.color=F,rot.per=0.3,colors=brewer.pal(8, "Dark2"))



freqs<-posterior(dtm_LDA)$terms[2,]

freqs<-sort(freqs,decreasing=T)
terms<-names(freqs)

select=order(-freqs)[1:100]
terms=terms[select]
freqs=freqs[select]

wordcloud(terms,freqs,random.order=F,random.color=F,rot.per=0.3,colors=brewer.pal(8, "Dark2"))

freqs<-posterior(dtm_LDA)$terms[3,]

freqs<-sort(freqs,decreasing=T)
terms<-names(freqs)

select=order(-freqs)[1:100]
terms=terms[select]
freqs=freqs[select]

wordcloud(terms,freqs,random.order=F,random.color=F,rot.per=0.3,colors=brewer.pal(8, "Dark2"))

freqs<-posterior(dtm_LDA)$terms[4,]

freqs<-sort(freqs,decreasing=T)
terms<-names(freqs)

select=order(-freqs)[1:100]
terms=terms[select]
freqs=freqs[select]

wordcloud(terms,freqs,random.order=F,random.color=F,rot.per=0.3,colors=brewer.pal(8, "Dark2"))

freqs<-posterior(dtm_LDA)$terms[5,]

freqs<-sort(freqs,decreasing=T)
terms<-names(freqs)

select=order(-freqs)[1:100]
terms=terms[select]
freqs=freqs[select]

wordcloud(terms,freqs,random.order=F,random.color=F,colors=brewer.pal(8, "Dark2"))


freqs<-posterior(dtm_LDA)$terms[6,]

freqs<-sort(freqs,decreasing=T)
terms<-names(freqs)

select=order(-freqs)[1:100]
terms=terms[select]
freqs=freqs[select]

wordcloud(terms,freqs,random.order=F,scale=c(3,0.1),random.color=F,colors=brewer.pal(8, "Dark2"))




freqs<-posterior(dtm_LDA)$terms[7,]

freqs<-sort(freqs,decreasing=T)
terms<-names(freqs)

select=order(-freqs)[1:100]
terms=terms[select]
freqs=freqs[select]

wordcloud(terms,freqs,random.order=F,random.color=F,colors=brewer.pal(8, "Dark2"))

freqs<-posterior(dtm_LDA)$terms[8,]

freqs<-sort(freqs,decreasing=T)
terms<-names(freqs)

select=order(-freqs)[1:100]
terms=terms[select]
freqs=freqs[select]

wordcloud(terms,freqs,random.order=F,random.color=F,colors=brewer.pal(8, "Dark2"))


freqs<-posterior(dtm_LDA)$terms[9,]

freqs<-sort(freqs,decreasing=T)
terms<-names(freqs)

select=order(-freqs)[1:100]
terms=terms[select]
freqs=freqs[select]

wordcloud(terms,freqs,random.order=F,random.color=F,colors=brewer.pal(8, "Dark2"))


############################Web Application for deeper analysis#############################

##link between JSON object and LDA output
install.packages("LDAvis")
library(LDAvis)
topicmodels_json_ldavis <- function(fitted, corpus, doc_term){
     # Required packages
     library(topicmodels)
     library(dplyr)
     library(stringi)
     library(tm)
     library(LDAvis)


     phi <- posterior(fitted)$terms %>% as.matrix
     theta <- posterior(fitted)$topics %>% as.matrix
   vocab <- colnames(phi)
    doc_length <- vector()
     for (i in 1:length(corpus)) {
         temp <- paste(corpus[[i]]$content, collapse = ' ')
         doc_length <- c(doc_length, stri_count(temp, regex = '\\S+'))
     }
     temp_frequency <- inspect(doc_term)
     freq_matrix <- data.frame(ST = colnames(temp_frequency),
                               Freq = colSums(temp_frequency))
     rm(temp_frequency)
 
     # Convert to json
     json_lda <- LDAvis::createJSON(phi = phi, theta = theta,
                             vocab = vocab,
                             doc.length = doc_length,
                             term.frequency = freq_matrix$Freq)
 
     return(json_lda)
 }

webapplication<-topicmodels_json_ldavis(dtm_LDA,docs,dtm2)
install.packages("servr")
library(servr)
serVis(webapplication,as.gist=TRUE,open.browser=TRUE,description='DPH')


###########################Tempotal Dynamic analysis#################################


###Posterior Gamma Process distribution parameter,fit a intensity function for each topic by data and estimated two parameter, exponential decay self-exciting point process#########################




################skip##########################


Beta <- dtm_LDA@gamma   #topic (main parameter)
Alpha<-dtm_LDA@beta     #term
Alpha<-exp(Alpha)
Beta <- as.data.frame(Beta)
Alpha<-as.data.frame(Alpha)


###verify and test model fitting

harmonicMean <- function(logLikelihoods, precision=2000L) 
{
   library("Rmpfr")
   llMed <- median(logLikelihoods)
   as.double(llMed - log(mean(exp(-mpfr(logLikelihoods,
                                        prec = precision) + llMed))))
 }
 k = 9
burnin = 1000
iter = 1000
keep = 50


##harmonic mean calculation!!!!!!!!!!!!!!!!!!##select sample test set for cross validation##

########skip!!!!!!!!!!!!##############
folding <- sample(rep(seq_len(10),ceiling(nrow(dtm2)))[seq_len(nrow(dtm2))])
testing<-which(folding==10)
training<-which(folding!=10)

fitted <- LDA(dtm2[training,], k = k, method = "Gibbs",control = list(burnin = burnin, iter = iter, keep = keep) )
logLiks <- fitted@logLiks[-c(1:(burnin/keep))]
harmonicMean(logLiks)


sequ <- seq(2, 50, 1)
fitted_many <- lapply(sequ, function(k) LDA(dtm2[training,], k = k, method = "Gibbs",control = list(burnin = burnin, iter = iter, keep = keep) ))

logLiks_many <- lapply(fitted_many, function(L)  L@logLiks[-c(1:(burnin/keep))])
hm_many <- sapply(logLiks_many, function(h) harmonicMean(h))

plot(sequ,hm_many,type='l',lwd=3,col='red',ylab="Harmonic Mean",xlab="Number of Topic",main="Maximum LogLikelihood")

sequ[which.max(hm_many)]


#!!!!!!!!!!!!!!!!!time consuming......haven't fit the mean model yet############




#################################skip end##################

##############parameter estimation for triggering kernel and Hawkes########
Beta <- dtm_LDA@gamma   #topic (main parameter)
Alpha<-dtm_LDA@beta     #term
Alpha<-exp(Alpha)
Beta <- as.data.frame(Beta)
Alpha<-as.data.frame(Alpha)




phi1<-Alpha[1,] 
phi1<-as.numeric(phi1)     #parameter  term
phi2<-Alpha[2,]
phi2<-as.numeric(phi2) 
phi3<-Alpha[3,]
phi3<-as.numeric(phi3) 

phi4<-Alpha[4,] 
phi4<-as.numeric(phi4)     #parameter  term
phi5<-Alpha[5,]
phi5<-as.numeric(phi5) 
phi6<-Alpha[6,]
phi6<-as.numeric(phi6)









Beta1<-Beta[,1]
Beta1<-as.numeric(Beta1)       #parameter  topic
Beta2<-Beta[,2]
Beta2<-as.numeric(Beta2)
Beta3<-Beta[,3]
Beta3<-as.numeric(Beta3)

Beta4<-Beta[,4]
Beta4<-as.numeric(Beta4)       #parameter  topic
Beta5<-Beta[,5]
Beta5<-as.numeric(Beta5)
Beta6<-Beta[,6]
Beta6<-as.numeric(Beta6)


     

Beta1<-mean(Beta1)
phi1<-mean(phi1)

Beta2<-mean(Beta2)
phi2<-mean(phi2)
Beta2
phi2

Beta3<-mean(Beta3)
phi3<-mean(phi3)
Beta3
phi3

Beta4<-mean(Beta4)
phi4<-mean(phi4)
Beta4
phi4

###################################topic1 intensity




df<-data.frame(id=names(Topic),
  data=as.POSIXct(unlist(lapply(meta(docs,tag="datetimestamp"),as.character)),
   origin=unlist(meta(docs,tag="origin")) ))


dft<-cbind(df,posterior(dtm_LDA)$topics)


dft<-cbind(df,posterior(dtm_LDA)$topics)
x.df<-data.frame(dft)


topic1<-x.df[,2:3]
A<-matrix(data=NA,nrow=1205,ncol=2)


topic2<-x.df[,c(2,4)]
B<-matrix(data=NA,nrow=1205,ncol=2)


topic3<-x.df[,c(2,5)]
C<-matrix(data=NA,nrow=1205,ncol=2)


topic4<-x.df[,2:6]
D<-matrix(data=NA,nrow=1205,ncol=2)


topic5<-x.df[,c(2,7)]
E<-matrix(data=NA,nrow=1205,ncol=2)

topic8<-x.df[,c(2,10)]
K<-matrix(data=NA,nrow=1205,ncol=2)


topic9<-x.df[,c(2,11)]
G<-matrix(data=NA,nrow=1205,ncol=2)




index1<-order(topic1$X1)
index1<-index[1:1205]
index1<-sort(index1)   #topic1 with respect to time
A<-topic1[index1,]
topic1<-A


index2<-order(topic2$X2)
index2<-index[1:1205]
index2<-sort(index2)   #topic1 with respect to time
B<-topic2[index2,]
topic2<-B


index3<-order(topic3$X3)
index3<-index[1:1205]
index3<-sort(index3)   #topic1 with respect to time
C<-topic3[index3,]
topic3<-C



index4<-order(topic4$X4)
index4<-index[1:1205]
index4<-sort(index4)   #topic1 with respect to time
D<-topic4[index4,]
topic4<-D


index5<-order(topic5$X5)
index5<-index[1:1205]
index5<-sort(index5)   #topic1 with respect to time
E<-topic5[index5,]
topic5<-E


index8<-order(topic8$X8)
index8<-index[1:1205]
index8<-sort(index8)   #topic1 with respect to time
K<-topic8[index8,]
topic8<- K



index9<-order(topic9$X9)
index9<-index[1:1205]
index9<-sort(index9)   #topic1 with respect to time
G<-topic9[index9,]
topic9<- G




time1<-topic1$data
time1<-as.Date(time1)
save<-time1
topic1$data<-time1

time2<-topic2$data
time2<-as.Date(time2)
save2<-time2
topic2$data<-time2


time3<-topic3$data
time3<-as.Date(time3)
save3<-time3
topic3$data<-time3



time4<-topic4$data
time4<-as.Date(time4)
save<-time4
topic4$data<-time4

time5<-topic5$data
time5<-as.Date(time5)
save5<-time5
topic5$data<-time5

time8<-topic8$data
time8<-as.Date(time8)
save8<-time8
topic8$data<-time8


time9<-topic9$data
time9<-as.Date(time9)
save9<-time9
topic9$data<-time9
#############################

library(dplyr)

new<-group_by(topic1,data)
timeseries1<-summarize(new,mean(X1))
topic1<-timeseries1
topic1<-topic1$"mean(X1)"


time<-seq(from=0,to=0.99,by=0.01)
intensity<-seq(from=0,to=0.99,by=0.01)
for(i in 1:99)
{
	intensity[i]<-topic1[i]*(99-i)
	
}



new2<-group_by(topic2,data)
timeseries2<-summarize(new2,mean(X2))
topic2<-timeseries2
topic2<-topic2$"mean(X2)"


time2<-seq(from=0,to=0.99,by=0.01)
intensity2<-seq(from=0,to=0.99,by=0.01)
for(i in 1:99)
{
	intensity2[i]<-topic2[i]*(99-i)
	
}

new3<-group_by(topic3,data)
timeseries3<-summarize(new3,mean(X3))
topic3<-timeseries3
topic3<-topic3$"mean(X3)"


time3<-seq(from=0,to=0.99,by=0.01)
intensity3<-seq(from=0,to=0.99,by=0.01)
for(i in 1:99)
{
	intensity3[i]<-topic3[i]*(99-i)
	
}


new4<-group_by(topic4,data)
timeseries4<-summarize(new4,mean(X4))
topic4<-timeseries4
topic4<-topic4$"mean(X4)"


time4<-seq(from=0,to=0.99,by=0.01)
intensity4<-seq(from=0,to=0.99,by=0.01)
for(i in 1:99)
{
	intensity4[i]<-topic4[i]*(99-i)
	
}

new5<-group_by(topic5,data)
timeseries5<-summarize(new5,mean(X5))
topic5<-timeseries5
topic5<-topic5$"mean(X5)"


time5<-seq(from=0,to=0.99,by=0.01)
intensity5<-seq(from=0,to=0.99,by=0.01)
for(i in 1:99)
{
	intensity5[i]<-topic5[i]*(99-i)
	
}

new8<-group_by(topic8,data)
timeseries8<-summarize(new8,mean(X8))
topic8<-timeseries8
topic8<-topic8$"mean(X8)"


time8<-seq(from=0,to=0.99,by=0.01)
intensity8<-seq(from=0,to=0.99,by=0.01)
for(i in 1:99)
{
	intensity8[i]<-topic8[i]*(99-i)
	
}


new9<-group_by(topic9,data)
timeseries9<-summarize(new9,mean(X9))
topic9<-timeseries9
topic9<-topic9$"mean(X9)"


time9<-seq(from=0,to=0.99,by=0.01)
intensity9<-seq(from=0,to=0.99,by=0.01)
for(i in 1:99)
{
	intensity9[i]<-topic9[i]*(99-i)
	
}


par(mfrow=c(2,3))
x<-seq(from=1,to=100,by=1)
plot(intensity~x,xlim=c(0,100),ylim=c(0,15),pch='.',col='red',xlab="Time",ylab='Intensity')
lines(intensity,col='red4')

x<-seq(from=1,to=100,by=1)
plot(intensity2~x,xlim=c(0,100),ylim=c(0,15),pch='.',xlab="Time",ylab='Intensity')
lines(intensity2,col='red4')



x<-seq(from=1,to=100,by=1)
plot(intensity3~x,xlim=c(0,100),ylim=c(0,30),pch='.',xlab="Time",ylab='Intensity')
lines(intensity3,col='red4')


x<-seq(from=1,to=100,by=1)
plot(intensity4~x,xlim=c(0,100),ylim=c(0,30),pch='.',xlab="Time",ylab='Intensity')
lines(intensity4,col='red4')


x<-seq(from=1,to=100,by=1)
plot(intensity5~x,xlim=c(0,100),ylim=c(0,30),pch='.',xlab="Time",ylab='Intensity')
lines(intensity5,col='red4')

x<-seq(from=1,to=100,by=1)
plot(intensity8~x,xlim=c(0,100),ylim=c(0,30),pch='.',xlab="Time",ylab='Intensity')
lines(intensity8,col='red4')

x<-seq(from=1,to=100,by=1)
plot(intensity9~x,xlim=c(0,100),ylim=c(0,30),pch='.',xlab="Time",ylab='Intensity')
lines(intensity9,col='red4')



	


##############################################
set.seed(0908)
folding <- sample(rep(seq_len(10),ceiling(nrow(dtm2)))[seq_len(nrow(dtm2))])
#topics <- 10 * c(1:5, 10, 20)
k<-9

train<-seq(from=1,to=20,by=1)
perp<-seq(from=1,to=20,by=1)

train1 <- LDA(dtm2, k = 4,control = list(verbose = 100, estimate.alpha = FALSE))
result<-perplexity(train1)
result









