varying vec3 ec_vnormal, ec_vposition;

void main(){
	vec3 P, N, L, V, H;
	vec4 diffuse_color = gl_FrontMaterial.diffuse * gl_LightSource[0].diffuse;
	vec4 specular_color = gl_FrontMaterial.specular * gl_LightSource[0].specular; 
	vec4 ambient_color = gl_FrontMaterial.ambient * gl_LightSource[0].ambient; 

	float shininess = gl_FrontMaterial.shininess;

	float atten, spot;
	float pi = 3.14159265;

	P = ec_vposition;
	N = normalize(ec_vnormal);
	L = normalize(gl_LightSource[0].position.xyz - P);
	V = normalize(-P);
	H = normalize(L+V);

	diffuse_color *= max(dot(N,L),0.2);

	specular_color *= ((shininess+2.0)/(8.0 * pi)) * pow(max(dot(H,N),0.0), shininess);

	float d = length(gl_LightSource[0].position - vec4(P, 1.0));

	atten = 1.0 / (gl_LightSource[0].constantAttenuation + 
		gl_LightSource[0].linearAttenuation * d + 
		gl_LightSource[0].quadraticAttenuation * d * d);

	gl_FragColor = ambient_color + atten * (diffuse_color + specular_color);
}
