varying vec3 ec_vnormal, ec_vposition;

void main(){
	ec_vnormal = gl_NormalMatrix * gl_Normal;
	ec_vposition = vec3((gl_ModelViewMatrix * gl_Vertex).xyz);
	gl_FrontColor = gl_Color;
	gl_Position = gl_ProjectionMatrix * gl_ModelViewMatrix * gl_Vertex;
}
