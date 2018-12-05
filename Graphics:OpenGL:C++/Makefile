CC		= gcc
C		= c

CFLAGS		= -g
LFLAGS		= -g

ifeq ("$(shell uname)", "Darwin")
  LDFLAGS     = -framework Foundation -framework GLUT -framework OpenGL -lm
else
  ifeq ("$(shell uname)", "Linux")
    LDFLAGS   = -I/usr/include -I/usr/X11R6/include -L/usr/lib -L/usr/X11R6/lib -O2 -lX11 -lGL -lGLU -lglut -lm -lXmu -lXi
  endif
endif

PROJECT		= teapot

${PROJECT}:	${PROJECT}.o
	${CC} ${LFLAGS} -o ${PROJECT} ${PROJECT}.o ${LDFLAGS}

${PROJECT}.o:	${PROJECT}.${C}
	${CC} ${CFLAGS} -c ${PROJECT}.${C}

clean:
	rm -f core.* *.o *~ ${PROJECT}
