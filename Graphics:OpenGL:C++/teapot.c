#include <GL/gl.h>
#include <GL/glu.h>
#include <GL/glut.h>
#include <GL/glx.h>
#include <GL/glext.h>

#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <math.h>
#include <fcntl.h>
#include <unistd.h>

#define XRES 1280
#define YRES 1024
#define PI 3.14159265359
#define BOX 1.0

unsigned int box, teapot;

typedef struct vector3f {
	GLfloat x, y, z;
} Vector3f;

typedef struct vector2f {
	GLfloat x, y;
} Vector2f;

struct teapot {
	Vector3f* vertex, *normal, *tangent, *bitangent;
	Vector2f* texture;
	GLuint* faceVertex;
	int vertexCount, textureCount;
};

struct box {
	Vector3f* vertex, *normal, *color;
	GLubyte* index;
};

struct toplight {
	Vector3f* vertex, *normal, *color;
	GLubyte* index;
};

struct teapot myTeapot;
struct box myBox;
struct toplight myToplight;

struct material {
	float ambientMat[4];
	float diffuseMat[4];
	float specularMat[4];
	float shininessMat[1];
};

struct material teapotMtl;

void loadTeapot(const char* filename) {
	FILE* fptr = fopen(filename, "r");
	char buf[256], *parse1;
	while (fgets(buf, sizeof(buf), fptr)) {
		parse1 = strtok(buf, " \t");
		if (!strcmp(parse1, "v")) {
			myTeapot.vertexCount++;
			continue;
		} else if (!strcmp(parse1, "vt")) {
			myTeapot.textureCount++;
			continue;
		}
	}
	myTeapot.vertex = malloc(sizeof(Vector3f) * myTeapot.vertexCount);
	myTeapot.texture = malloc(sizeof(Vector2f) * myTeapot.textureCount);
	myTeapot.normal = malloc(sizeof(Vector3f) * myTeapot.vertexCount);
	Vector2f* textureInOrder = malloc(sizeof(Vector2f) * myTeapot.textureCount);
	Vector3f* normalInOrder = malloc(sizeof(Vector3f) * myTeapot.vertexCount);
	myTeapot.tangent = malloc(sizeof(Vector3f) * myTeapot.vertexCount);
	myTeapot.bitangent = malloc(sizeof(Vector3f) * myTeapot.vertexCount);
	myTeapot.faceVertex = malloc(sizeof(GLuint) * myTeapot.vertexCount * 4);
	int vertexIndex = 0, texIndex = 0, normalIndex = 0, tangentIndex = 0, bitangentIndex = 0, faceIndex = 0;
	char *parse, * face[12];
	rewind(fptr);

	while (fgets(buf, 3, fptr)) {
		if (buf[0] == '#' && buf[1] == ' ') {
			fgets(buf, sizeof(buf), fptr);
			continue;
		} else if (buf[0] == 'v' && buf[1] == ' ') {
			fscanf(fptr, "%f %f %f", &myTeapot.vertex[vertexIndex].x, &myTeapot.vertex[vertexIndex].y, &myTeapot.vertex[vertexIndex].z);
			vertexIndex++;
			continue;
		} else if (buf[0] == 'v' && buf[1] == 't') {
			fscanf(fptr, "%f %f", &textureInOrder[texIndex].x, &textureInOrder[texIndex].y);
			texIndex++;
			continue;
		} else if (buf[0] == 'v' && buf[1] == 'n') {
			fscanf(fptr, "%f %f %f", &normalInOrder[normalIndex].x, &normalInOrder[normalIndex].y, &normalInOrder[normalIndex].z);
			normalIndex++;
			continue;
		} else if (buf[0] == 'v' && buf[1] == 'x') {
			fscanf(fptr, "%f %f %f", &myTeapot.tangent[tangentIndex].x, &myTeapot.tangent[tangentIndex].y, &myTeapot.tangent[tangentIndex].z);
			tangentIndex++;
			continue;
		} else if (buf[0] == 'v' && buf[1] == 'y') {
			fscanf(fptr, "%f %f %f", &myTeapot.bitangent[bitangentIndex].x, &myTeapot.bitangent[bitangentIndex].y, &myTeapot.bitangent[bitangentIndex].z);
			bitangentIndex++;
			continue;
		} else if (buf[0] == 'f' && buf[1] == ' ') {
			fgets(buf, sizeof(buf), fptr);
			parse = strtok(buf, " /");
			face[0] = parse;
			int i;
			for (i = 1; i < 12; i++) {
				parse = strtok(NULL, " /");
				face[i] = parse;
			}
			for (i = 0; i < 12; i += 3) {
				int currVertex = atoi(face[i]) - 1;
				myTeapot.faceVertex[faceIndex] = currVertex;
				myTeapot.texture[currVertex] = textureInOrder[atoi(face[i + 1]) - 1];
				myTeapot.normal[currVertex] = normalInOrder[atoi(face[i + 2]) - 1];
				faceIndex++;
			}
			continue;
		}
	}
	fclose(fptr);
}

void loadTeapotMtl(const char* filename) {
	FILE* fptr = fopen(filename, "r");
	char buf[256], *parse;
	int i;
	while (fgets(buf, sizeof(buf), fptr)) {
		if (buf[0] == '#') continue;
		parse = strtok(buf, " \t");
		if (!strcmp(parse, "newmtl")) {
			continue;
		} else if (!strcmp(parse, "Ka")) {
			for (i = 0; i < 3; ++i) {
				parse = strtok(NULL, " \t");
				teapotMtl.ambientMat[i] = atof(parse);
			}
			teapotMtl.ambientMat[3] = 1.0;
		} else if (!strcmp(parse, "Kd")) {
			for (i = 0; i < 3; ++i) {
				parse = strtok(NULL, " \t");
				teapotMtl.diffuseMat[i] = atof(parse);
			}
			teapotMtl.diffuseMat[3] = 1.0;
		} else if (!strcmp(parse, "Ks")) {
			for (i = 0; i < 3; ++i) {
				parse = strtok(NULL, " \t");
				teapotMtl.specularMat[i] = atof(parse);
			}
			teapotMtl.specularMat[3] = 1.0;
		} else if (!strcmp(parse, "Ns")) {
			parse = strtok(NULL, " \t");
			teapotMtl.shininessMat[1] = atof(parse);
		}
	}
}

void loadBox() {
	myBox.vertex = malloc(sizeof(Vector3f) * 24);
	myBox.normal = malloc(sizeof(Vector3f) * 24);
	myBox.color = malloc(sizeof(Vector3f) * 24);
	myBox.index = malloc(sizeof(GLubyte) * 24);

	Vector3f vertex[] = {{ -BOX, -BOX, -BOX}, { -BOX, -BOX, BOX}, {BOX, -BOX, BOX}, {BOX, -BOX, -BOX},
		{ -BOX, -BOX, -BOX}, { -BOX, BOX, -BOX}, {BOX, BOX, -BOX}, {BOX, -BOX, -BOX},
		{ -BOX, -BOX, -BOX}, { -BOX, -BOX, BOX}, { -BOX, BOX, BOX}, { -BOX, BOX, -BOX},
		{BOX, -BOX, -BOX}, {BOX, BOX, -BOX}, {BOX, BOX, BOX}, {BOX, -BOX, BOX},
		{ -BOX, BOX, -BOX}, { -BOX, BOX, BOX}, {BOX, BOX, BOX}, {BOX, BOX, -BOX},
		{ -BOX, -BOX, BOX}, {BOX, -BOX, BOX}, {BOX, BOX, BOX}, { -BOX, BOX, BOX}
	};

	Vector3f normal[] = { {0.0, 1.0, 0.0}, {0.0, 1.0, 0.0}, {0.0, 1.0, 0.0}, {0.0, 1.0, 0.0},
		{0.0, 0.0, 1.0}, {0.0, 0.0, 1.0}, {0.0, 0.0, 1.0}, {0.0, 0.0, 1.0},
		{1.0, 0.0, 0.0}, {1.0, 0.0, 0.0}, {1.0, 0.0, 0.0}, {1.0, 0.0, 0.0},
		{ -1.0, 0.0, 0.0}, { -1.0, 0.0, 0.0}, { -1.0, 0.0, 0.0}, { -1.0, 0.0, 0.0},
		{0.0, -1.0, 0.0}, {0.0, -1.0, 0.0}, {0.0, -1.0, 0.0}, {0.0, -1.0, 0.0},
		{0.0, 0.0, -1.0}, {0.0, 0.0, -1.0}, {0.0, 0.0, -1.0}, {0.0, 0.0, -1.0}
	};

	Vector3f color[] = { {1.0, 1.0, 1.0}, {1.0, 1.0, 1.0}, {1.0, 1.0, 1.0}, {1.0, 1.0, 1.0},
		{1.0, 1.0, 1.0}, {1.0, 1.0, 1.0}, {1.0, 1.0, 1.0}, {1.0, 1.0, 1.0},
		{1.0, 0.0, 0.0}, {1.0, 0.0, 0.0}, {1.0, 0.0, 0.0}, {1.0, 0.0, 0.0},
		{0.0, 1.0, 0.0}, {0.0, 1.0, 0.0}, {0.0, 1.0, 0.0}, {0.0, 1.0, 0.0},
		{1.0, 1.0, 1.0}, {1.0, 1.0, 1.0}, {1.0, 1.0, 1.0}, {1.0, 1.0, 1.0},
		{1.0, 1.0, 1.0}, {1.0, 1.0, 1.0}, {1.0, 1.0, 1.0}, {1.0, 1.0, 1.0}
	};

	int j;

	for (j = 0; j < 24; j++) {
		myBox.vertex[j] = vertex[j];
		myBox.normal[j] = normal[j];
		myBox.color[j] = color[j];
		myBox.index[j] = j;
	}
}

void loadToplight() {
	Vector3f vertex[4] = {{ -0.2, 1.0, -0.2}, { -0.2, 1.0, 0.2}, {0.2, 1.0, 0.2}, {0.2, 1.0, -0.2}};
	Vector3f normal[4] = {{ 0.0, -1.0, 0.0}, { 0.0, -1.0, 0.0}, { 0.0, -1.0, 0.0}, { 0.0, -1.0, 0.0}};
	Vector3f color[4] = {{0.0, 0.0, 1.0}, {0.0, 0.0, 1.0}, {0.0, 0.0, 1.0}, {0.0, 0.0, 1.0}};

	myToplight.vertex = malloc(sizeof(Vector3f) * 4);
	myToplight.normal = malloc(sizeof(Vector3f) * 4);
	myToplight.color = malloc(sizeof(Vector3f) * 4);
	myToplight.index = malloc(sizeof(GLubyte) * 4);

	int i;
	for (i = 0; i < 4; i++) {
		myToplight.vertex[i] = vertex[i];
		myToplight.normal[i] = normal[i];
		myToplight.color[i] = color[i];
		myToplight.index[i] = i;
	}
}

char* readShaderProgram(char *filename) {
	FILE *fp;
	char *content = NULL;
	int fd, count;
	fd = open(filename, O_RDONLY);
	count = lseek(fd, 0, SEEK_END);
	close(fd);
	content = (char *)calloc(1, (count + 1));
	fp = fopen(filename, "r");
	count = fread(content, sizeof(char), count, fp);
	content[count] = '\0';
	fclose(fp);
	return content;
}

unsigned int setShaders(char* vertexShader, char* fragmentShader) {
	GLint vertCompiled, fragCompiled;
	char *vs, *fs;
	GLuint v, f, p;
	int result = -1;

	v = glCreateShader(GL_VERTEX_SHADER);
	f = glCreateShader(GL_FRAGMENT_SHADER);
	vs = readShaderProgram(vertexShader);
	fs = readShaderProgram(fragmentShader);
	glShaderSource(v, 1, (const char **)&vs, NULL);
	glShaderSource(f, 1, (const char **)&fs, NULL);
	free(vs);
	free(fs);
	glCompileShader(v);
	glCompileShader(f);

	glGetShaderiv(f, GL_COMPILE_STATUS, &result);
	fprintf(stderr, "compiled result (1: compile; 0: fail): %d\n", result);
	GLchar vinfoLog[512];

	glGetShaderiv(v, GL_COMPILE_STATUS, &vertCompiled);
	if (!vertCompiled)
	{
		glGetShaderInfoLog(v, 512, NULL, vinfoLog);
		fprintf(stderr, "ERROR::SHADER::VERTEX::COMPILATION_FAILED:  %s\n", vinfoLog);
	}
	GLchar finfoLog[512];
	glGetShaderiv(f, GL_COMPILE_STATUS, &fragCompiled);
	if (!fragCompiled)
	{
		glGetShaderInfoLog(f, 512, NULL, finfoLog);
		fprintf(stderr, "ERROR::SHADER::FRAGMENT::COMPILATION_FAILED:  %s\n", finfoLog);
	}

	p = glCreateProgram();
	glAttachShader(p, f);
	glAttachShader(p, v);
	glLinkProgram(p);

	GLint programLinked;
	GLchar pinfoLog[512];
	glGetProgramiv(p, GL_LINK_STATUS, &programLinked);
	if (!programLinked) {
		glGetProgramInfoLog(p, 512, NULL, pinfoLog);
		fprintf(stderr, "ERROR::SHADER::PROGRAM::LINK_FAILED:  %s", pinfoLog);
	}
	return (p);
}

void setupViewVolume(float *ep, float *vp) {
	glMatrixMode(GL_PROJECTION);
	glLoadIdentity();
	gluPerspective(45.0, (float)(XRES) / (float)(YRES), 1.0, 20.0);
	glMatrixMode(GL_MODELVIEW);
	glLoadIdentity();
	gluLookAt(ep[0], ep[1], ep[2], vp[0], vp[1], vp[2], 0.0, 1.0, 0.0);
}

float phi(int b, int i) {
	float x, f;
	x = 0.0;
	f = 1.0 / (float)b;
	while (i) {
		x += f * (float)(i % b);
		i /= b;
		f *= 1.0 / (float)b;
	}
	return x;
}

Vector3f randomPoint(int i) {
	float az, el;
	Vector3f dp;

	az = 2.0 * PI * phi(2, i);
	el = asin(phi(3, i));
	dp.x = -sin(az) * cos(el);
	dp.y = 0.7;
	dp.z = cos(az) * cos(el);
	return dp;
}

Vector3f randomRay(int i) {
	float az, el;
	Vector3f dp;

	az = 2.0 * PI * phi(5, i);
	el = asin(phi(7, i));
	dp.x = -sin(az) * cos(el);
	dp.y = -sin(el) * (rand()%2 ? 1.0 : -1.0);
	dp.z = cos(az) * cos(el);
	return dp;
}

Vector3f addition(Vector3f vec1, Vector3f vec2) {
	Vector3f result = { vec1.x + vec2.x, vec1.y + vec2.y, vec1.z + vec2.z};
	return result;
}

Vector3f substraction(Vector3f vec1, Vector3f vec2) {
	Vector3f result = { vec1.x - vec2.x, vec1.y - vec2.y, vec1.z - vec2.z};
	return result;
}

float dot(Vector3f vec1, Vector3f vec2) {
	return vec1.x * vec2.x + vec1.y * vec2.y + vec1.z * vec2.z;
}

Vector3f multiplyScalar(Vector3f vec, float scale) {
	Vector3f result = { vec.x * scale, vec.y * scale, vec.z * scale};
	return result;
}

int inBox(Vector3f vec) {
	if (vec.x >= BOX || vec.x <= -BOX || vec.y >= BOX || vec.y <= -BOX || vec.z >= BOX || vec.z <= -BOX) {
		return 0;
	}
	return 1;
}

Vector3f intersectionPoint(Vector3f p0, Vector3f normal, Vector3f l, Vector3f l0) {
	Vector3f result;
	Vector3f oops = { -1000.0, -1000.0, -1000.0 };
	float nominator = dot(substraction(p0, l0), normal);
	float denominator = dot(l, normal);
	if (denominator == 0.0) {
		return oops;
	} else {
		result = addition(multiplyScalar(l, nominator / denominator), l0);
	}
	if (inBox(result)) {
		return result;
	} else {
		return oops;
	}
}

Vector3f reflect(Vector3f lightPos, Vector3f normal) {
	return substraction(lightPos, multiplyScalar(normal, 2 * dot(lightPos, normal)));
}

void setReflectedLight(int index, Vector3f lightPos, Vector3f lightDir) {
	float position[] = {lightPos.x, lightPos.y, lightPos.z, 1.0};
	float direction[] = {lightDir.x, lightDir.y, lightDir.z, 1.0};
	float diffuse[] = {myBox.color[index].x * 0.5, myBox.color[index].y * 0.5, myBox.color[index].z * 0.5, 1.0};
	float specular[] = {myBox.color[index].x * 0.2, myBox.color[index].y * 0.2, myBox.color[index].z * 0.2, 1.0};

	glLightModeli(GL_LIGHT_MODEL_LOCAL_VIEWER, 1);
	glLightfv(GL_LIGHT0, GL_POSITION, position);
	glLightfv(GL_LIGHT0, GL_SPOT_DIRECTION, direction);
	glLightfv(GL_LIGHT0, GL_DIFFUSE, diffuse);
	glLightfv(GL_LIGHT0, GL_SPECULAR, specular);
	glLightf(GL_LIGHT0, GL_SPOT_EXPONENT, 30);
	glLightf(GL_LIGHT0, GL_CONSTANT_ATTENUATION, 1.1);
	glLightf(GL_LIGHT0, GL_LINEAR_ATTENUATION, 0.01);
	glLightf(GL_LIGHT0, GL_QUADRATIC_ATTENUATION, 0.0001);
	glEnable(GL_LIGHTING);
	glEnable(GL_LIGHT0);
}

void drawBox() {
	glEnableClientState(GL_COLOR_ARRAY);
	glEnableClientState(GL_VERTEX_ARRAY);
	glEnableClientState(GL_NORMAL_ARRAY);

	glVertexPointer(3, GL_FLOAT, 0, myBox.vertex);
	glColorPointer(3, GL_FLOAT, 0, myBox.color);
	glNormalPointer(GL_FLOAT, 0, myBox.normal);
	glDrawElements(GL_QUADS, 20, GL_UNSIGNED_BYTE, myBox.index);
}

void setTeapotMaterial() {
	glMaterialfv(GL_FRONT, GL_AMBIENT, teapotMtl.ambientMat);
	glMaterialfv(GL_FRONT, GL_DIFFUSE, teapotMtl.diffuseMat);
	glMaterialfv(GL_FRONT, GL_SPECULAR, teapotMtl.specularMat);
	glMaterialfv(GL_FRONT, GL_SHININESS, teapotMtl.shininessMat);
}

void drawTeapot() {
	glEnableClientState(GL_VERTEX_ARRAY);
	glEnableClientState(GL_NORMAL_ARRAY);
	glDisableClientState(GL_COLOR_ARRAY);

	setTeapotMaterial();
	glVertexPointer(3, GL_FLOAT, 0, myTeapot.vertex);
	glNormalPointer(GL_FLOAT, 0, myTeapot.normal);
	glPushMatrix();
	glScalef(0.4, 0.5, 0.4);
	glTranslatef(0.0, -1.8, 0.0);
	glRotatef(60.0, 0.0, 1.0, 0.0);
	glDrawElements(GL_QUADS, myTeapot.vertexCount * 4, GL_UNSIGNED_INT, myTeapot.faceVertex);
	glPopMatrix();
}

void setMainlight(float* mainLightPos, float* mainLightDir) {
	float diffuse[] = {1.0, 1.0, 1.0, 1.0};
	float ambient[] = {0.5, 0.5, 0.5, 1.0};
	float specular[] = {0.8, 0.8, 0.8, 1.0};

	glLightModeli(GL_LIGHT_MODEL_LOCAL_VIEWER, 1);
	glLightfv(GL_LIGHT0, GL_DIFFUSE, diffuse);
	glLightfv(GL_LIGHT0, GL_SPECULAR, specular);
	glLightfv(GL_LIGHT0, GL_AMBIENT, ambient);
	glLightf(GL_LIGHT0, GL_SPOT_EXPONENT, 16);
	glLightf(GL_LIGHT0, GL_CONSTANT_ATTENUATION, 0.5);
	glLightf(GL_LIGHT0, GL_LINEAR_ATTENUATION, 0.01);
	glLightf(GL_LIGHT0, GL_QUADRATIC_ATTENUATION, 0.001);
	glLightfv(GL_LIGHT0, GL_POSITION, mainLightPos);
	glLightfv(GL_LIGHT0, GL_SPOT_DIRECTION, mainLightDir);
	glEnable(GL_LIGHTING);
	glEnable(GL_LIGHT0);
}

void drawToplight() {
	glEnableClientState(GL_COLOR_ARRAY);
	glEnableClientState(GL_VERTEX_ARRAY);
	glEnableClientState(GL_NORMAL_ARRAY);

	glVertexPointer(3, GL_FLOAT, 0, myToplight.vertex);
	glColorPointer(3, GL_FLOAT, 0, myToplight.color);
	glNormalPointer(GL_FLOAT, 0, myToplight.normal);
	glDrawElements(GL_QUADS, 4, GL_UNSIGNED_BYTE, myToplight.index);
}

void renderScene() {
	glClear(GL_ACCUM_BUFFER_BIT);
	float eyepoint[3];
	float viewpoint[3];
	eyepoint[0] = 0.0; eyepoint[1] = 0.0; eyepoint[2] = 2.5;
	viewpoint[0] = 0.0; viewpoint[1] = 0.0; viewpoint[1] = 0.0;
	setupViewVolume(eyepoint, viewpoint);
	int i, j;
	Vector3f v0, v1, v2;
	unsigned int vpl = 0;
	while (vpl < 1000) {
		Vector3f point = randomPoint(i);
		Vector3f ray = randomRay(i);
		for (j = 0; j < 24; j += 4) {
			Vector3f lightPos = intersectionPoint( myBox.vertex[j], myBox.normal[j], ray, point);
			if (lightPos.x != -1000.0) {
				Vector3f lightDir = reflect(lightPos, myBox.normal[j]);
				setReflectedLight(j, lightPos, lightDir);
				glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);
				glUseProgram(box);
				drawBox();
				drawTeapot();
				glFlush();
				++vpl;
				glAccum(GL_ACCUM, 1.0 / (float) vpl * 0.7);
				break;
			} else {
				continue;
			}
		}
		++i;
	}
	float mainLightPos[] = {0.0, 0.9, 0.0, 1.0};
	float mainLightDir[] = {0.0, -0.9, 0.0, 1.0};
	setMainlight(mainLightPos, mainLightDir);

	glUseProgram(teapot);
	drawBox();
	drawTeapot();

	glFlush();
	glAccum(GL_ACCUM, 0.3);
	glAccum(GL_RETURN, 1.0);
	glutSwapBuffers();
}

void getout(unsigned char key, int x, int y) {
	switch (key) {
	case 'q':
		exit(1);
	default:
		break;
	}
}

int main(int argc, char **argv) {
	srand(time(0));
	glutInit(&argc, argv);
	glutInitDisplayMode(GLUT_RGBA | GLUT_DOUBLE | GLUT_DEPTH | GLUT_ACCUM | GLUT_MULTISAMPLE);
	glutInitWindowSize(XRES, YRES);
	glutInitWindowPosition(0, 0);
	glutCreateWindow("My Teapot");
	glClearColor(0.0, 1.0, 1.0, 0.0);
	glEnable(GL_DEPTH_TEST);
	glEnable(GL_MULTISAMPLE_ARB);
	loadTeapot("teapot.605.obj");
	loadTeapotMtl("teapot.605.mtl");
	loadBox();
	loadToplight();
	teapot = setShaders("teapot.vert", "teapot.frag");
	box = setShaders("box.vert", "box.frag");

	glutDisplayFunc(renderScene);
	glutKeyboardFunc(getout);
	glutMainLoop();
	return 0;
}
